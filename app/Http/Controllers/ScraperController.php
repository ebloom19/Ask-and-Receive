<?php

namespace App\Http\Controllers;

use Goutte\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\PropertyData;

class PropertyDetails
{
    public $accountNumber;
    public $balance;
}


class ScraperController extends Controller
{
    private $results = array();

    private $focusAddress = '';

    private $searchTerms = [];

    private $investorMetrics = [];

    private $proxy = '';

    private $numberOfBeds = '';

    private $states = [
        "NSW" => "New South Wales",
        "VIC" => "Victoria",
        "QLD" => "Queensland",
        "TAS" => "Tasmania",
        "SA" => "South Australia",
        "WA" => "Western Australia",
        "NT" => "Northern Territory",
        "ACT" => "Australian Capital Territory"
    ];

    private $streetTypes = ['Alley', 'Arcade', 'Avenue', 'Boulevard', 'Bypass', 'Circuit', 'Close', 'Corner', 'Court', 'Crescent', 'Cul-de-sac', 'Drive', 'Esplanade', 'Green', 'Grove', 'Highway', 'Junction', 'Lane', 'Link', 'Mews', 'Parade', 'Place', 'Ridge', 'Road', 'Square', 'Street', 'Terrace'];


    public function index() {

        $states = $this->states;
        $streetTypes = $this->streetTypes;

        return view('welcome', compact('states', 'streetTypes'));
    }

    public function results(Request $request){
        // Log::info(json_encode($request->al()));

        $states = $this->states;
        $streetTypes = $this->streetTypes;

        $this->proxy = env('PROXY');

        $streetNumber = $request->input('streetNumber');
        $unitNumber = $request->input('unitNumber');
        $streetName = strtoupper($request->input('streetName'));
        $streetType = $request->input('streetType');
        $suburb = str_replace(' ', '%20', $request->input('suburb'));
        $state = strtoupper($request->input('state'));
        $postCode = $request->input('postCode');

        $this->searchTerms = array (
            "streetNumber"=>$streetNumber,
            "unitNumber"=>$unitNumber,
            "streetName"=>$streetName,
            "streetType"=>$streetType,
            "suburb"=>$suburb,
            "state"=>$state,
            "postCode"=>$postCode
        );

        $this->focusAddress = "{$streetNumber} ";

        $client = new Client();
        $url = "https://www.oldlistings.com.au/real-estate/{$state}/{$suburb}/{$postCode}/buy/1/{$streetName}";
        $page = $client->request('GET', $url, ['proxy' => env('PROXY')]);

        $resultPages = $page->filter('.pagination > li')->each(function ($result) {
            return $result->text();
        });

        $resultPages = array_slice($resultPages,0,count($resultPages)-1);

        $resultPages = empty($resultPages) ? ['1'] : $resultPages;

        // echo "<pre>";
        // print_r($page);

        foreach($resultPages as $page) {
            $client = new Client();
            $url = "https://www.oldlistings.com.au/real-estate/{$state}/{$suburb}/{$postCode}/buy/{$page}/{$streetName}";
            $page = $client->request('GET', $url, ['proxy' => env('PROXY')]);

            $page->filter('.property')->each(function ($item) {
                if(substr($item->filter('.address')->text(), 0, strlen($this->focusAddress)) === $this->focusAddress) {
                    $propertyDetails = array();
        
                    $propertyDetails["propertyInfo"] = $item->filter('.property-meta')->each(function ($detail) {
                        $propertyDetails[$detail->filter('span')->text()] = $detail->text();

                        if(str_contains($detail->text(), 'Bed')) {
                            $this->numberOfBeds = preg_replace('/[^0-9]/', '', $detail->text());
                        };

                        return $detail->text();
                    });
        
                    $propertyDetails["listingHistory"] = $item->filter('li')->each(function ($listing) {
                        // $propertyDetails[$listing->filter('span')->text()] = $listing->text();
                        return $listing->text() != $listing->filter('span')->text() ? [$listing->filter('span')->text(), str_replace($listing->filter('span')->text(), '', $listing->text())] : [$listing->filter('span')->text()];
                    });
                    
                    $this->results[$item->filter('.address')->text()] = $propertyDetails;
                }
    
            });
        }

        // Rental History Check

        $client = new Client();
        $urlRent = "https://www.oldlistings.com.au/real-estate/{$state}/{$suburb}/{$postCode}/rent/1/{$streetName}";
        $page = $client->request('GET', $urlRent, ['proxy' => env('PROXY')]);

        $resultPages = $page->filter('.pagination > li')->each(function ($result) {
            return $result->text();
        });

        $resultPages = array_slice($resultPages,0,count($resultPages)-1);

        $resultPages = empty($resultPages) ? ['1'] : $resultPages;

        foreach($resultPages as $page) {
            $client = new Client();
            $url = "https://www.oldlistings.com.au/real-estate/{$state}/{$suburb}/{$postCode}/rent/{$page}/{$streetName}";
            $page = $client->request('GET', $url, ['proxy' => env('PROXY')]);

            $page->filter('.property')->each(function ($item) {
                if(substr($item->filter('.address')->text(), 0, strlen($this->focusAddress)) === $this->focusAddress) {
                    $propertyDetails = array();
        
                    // Need to account for if rental only found include details
                    // $propertyDetails["propertyInfo"] = $item->filter('.property-meta')->each(function ($detail) {
                    //     $propertyDetails[$detail->filter('span')->text()] = $detail->text();
                    //     return $detail->text();
                    // });
        
                    $propertyDetails["rentalHistory"] = $item->filter('li')->each(function ($listing) {
                        // $propertyDetails[$listing->filter('span')->text()] = $listing->text();
                        return $listing->text() != $listing->filter('span')->text() ? [$listing->filter('span')->text(), str_replace($listing->filter('span')->text(), '', $listing->text())] : [$listing->filter('span')->text()];
                    });

                    array_key_exists($item->filter('.address')->text(), $this->results) ? 
                        $this->results[$item->filter('.address')->text()] = $propertyDetails + $this->results[$item->filter('.address')->text()]
                        :
                        $this->results["{$item->filter('.address')->text()} - Rent"] = $propertyDetails;

                    // Call to get Suburb data
                    $url = "https://investor-api.realestate.com.au/v2/states/{$this->searchTerms["state"]}/suburbs/{$this->searchTerms["suburb"]}.json";
                    $response = file_get_contents($url);
                    $response = json_decode($response, true);

                    $suburb = strtoupper(str_replace('%20', ' ', $this->searchTerms['suburb']));

                    if($this->numberOfBeds >= 5) {
                        $this->numberOfBeds = "5+";
                    } elseif(!isset($this->numberOfBeds)) {
                        $this->numberOfBeds = "ALL";
                    } elseif($this->numberOfBeds == 1) {
                        if(!isset($this->searchTerms['unitNumber'])) {
                            $this->numberOfBeds = "ALL";
                        }
                    } 

                    if(isset($this->searchTerms['unitNumber'])) {
                        $this->investorMetrics = $response["{$suburb}-{$this->searchTerms['postCode']}"]["property_types"]["UNIT"]["bedrooms"][$this->numberOfBeds];
                    } else {
                        $this->investorMetrics = $response["{$suburb}-{$this->searchTerms['postCode']}"]["property_types"]["HOUSE"]["bedrooms"][$this->numberOfBeds];
                    }
                    
                }
    
            });
        }

        // $data = new PropertyData;
        // $data->property = $this->results;
        // $data->save();

        // "investor_metrics":{
        //     "median_sold_price":830000,
        //     "median_sold_price_five_years_ago":755000,
        //     "median_rental_price":620,
        //     "rental_yield":0.0388434,
        //     "annual_growth":0.019,
        //     "rental_demand":951.37,
        //     "rental_properties":126,
        //     "sold_properties":128,
        // }      

        $propertyData = $this->results;
        $investorMetrics = $this->investorMetrics;
        $numberOfBeds = $this->numberOfBeds;
        $searchTerms = $this->searchTerms;

        // Remove broken info 


        return "{$investorMetrics} AND {$numberOfBeds}";
        // return $propertyData;

        return view('welcome', compact('propertyData', 'states', 'streetTypes', 'investorMetrics', 'numberOfBeds', 'searchTerms'));
        // return view('scraper');
    }
}
