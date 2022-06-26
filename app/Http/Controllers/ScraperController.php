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

    public function scraper(Request $request){
        // Log::info(json_encode($request->al()));

        $states = $this->states;
        $streetTypes = $this->streetTypes;


        $streetNumber = $request->input('streetNumber');
        $unitNumber = $request->input('unitNumber');
        $streetName = strtoupper($request->input('streetName'));
        $suburb = str_replace(' ', '%20', $request->input('suburb'));
        $state = strtoupper($request->input('state'));
        $postCode = $request->input('postCode');

        $this->focusAddress = "{$streetNumber} ";

        $client = new Client();
        $url = "https://www.oldlistings.com.au/real-estate/{$state}/{$suburb}/{$postCode}/buy/1/{$streetName}";
        $page = $client->request('GET', $url);

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
            $page = $client->request('GET', $url);

            $page->filter('.property')->each(function ($item) {
                if(substr($item->filter('.address')->text(), 0, strlen($this->focusAddress)) === $this->focusAddress) {
                    $propertyDetails = array();
        
                    $propertyDetails["propertyInfo"] = $item->filter('.property-meta')->each(function ($detail) {
                        $propertyDetails[$detail->filter('span')->text()] = $detail->text();
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
        $page = $client->request('GET', $urlRent);

        $resultPages = $page->filter('.pagination > li')->each(function ($result) {
            return $result->text();
        });

        $resultPages = array_slice($resultPages,0,count($resultPages)-1);

        $resultPages = empty($resultPages) ? ['1'] : $resultPages;

        foreach($resultPages as $page) {
            $client = new Client();
            $url = "https://www.oldlistings.com.au/real-estate/{$state}/{$suburb}/{$postCode}/rent/{$page}/{$streetName}";
            $page = $client->request('GET', $url);

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
                    
                }
    
            });
        }

        // $data = new PropertyData;
        // $data->property = $this->results;
        // $data->save();

        $propertyData = $this->results;

        // Remove broken info 






        // return $propertyData;

        return view('welcome', compact('propertyData', 'states', 'streetTypes'));
        // return view('scraper');
    }
}
