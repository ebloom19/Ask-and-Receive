<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TodoListController;
use App\Spiders\LaravelDocsSpider;
use App\Http\Controllers\ScraperController;
use App\Http\Controllers\FormController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [ScraperController::class, 'index']);

// Route::get('/', function () {
//     return view('welcome');
// });

// Route::get('/saveItemRoute', [ScraperController::class, 'results'])->name('results');

Route::get('/results', [ScraperController::class, 'results'])->name('results');

// Route::get('/submit', 'App\Http\Controllers\FormController@submit');

Route::post('/results', 'ScraperController@results');
