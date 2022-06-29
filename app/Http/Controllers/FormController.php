<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FormController extends Controller
{
    public function submit(Request $request) {
        $this->validate($request, [
            'streetNumber' => 'required',
            // 'unitNumber' => 'required',
            'streetName' => 'required',
            'suburb' => 'required',
            'state' => 'required',
            'postCode' => 'required',
        ]);

        $streetNumber = $_GET['streetNumber'];
        $unitNumber = $_GET['unitNumber'];
        $streetName = strtoupper($_GET['streetName']);
        $streetType = $_GET['streetType'];
        $suburb = str_replace(' ', '%20', $_GET['suburb']);
        $state = strtoupper($_GET['state']);
        $postcode = $_GET['postCode'];
        

        $searchTerms = array (
            "streetNumber"=>$_GET['streetNumber'],
            "unitNumber"=>$_GET['unitNumber'],
            "streetName"=>strtoupper($_GET['streetName']),
            "streetType"=>$_GET['streetType'],
            "suburb"=>str_replace(' ', '%20', $_GET['suburb']),
            "state"=>strtoupper($_GET['state']),
            "postCode"=>$_GET['postCode'],
        );


        // response()->json(null, 200)

        return redirect()->route('results', compact('searchTerms'));
    }
}
