<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TodoListController extends Controller
{

    public function saveItem() {
        return view('welcome', compact(''));
    }
}
