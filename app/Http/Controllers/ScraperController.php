<?php

namespace App\Http\Controllers;

use Goutte\Client;
use Illuminate\Http\Request;

class PropertyDetails
{
    public $accountNumber;
    public $balance;
}


class ScraperController extends Controller
{
    private $results = array();

    public function scraper(){
        $client = new Client();
        $url = 'https://www.oldlistings.com.au/real-estate/WA/North+Beach/6020/buy/1/NORTH%20BEACH';
        $page = $client->request('GET', $url);

        // echo "<pre>";
        // print_r($page);

        $page->filter('.property')->each(function ($item) {
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
        });

        $data = $this->results;

        // return $data;

        return view('scraper', compact('data'));
        // return view('scraper');
    }
}
