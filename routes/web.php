<?php
use App\Http\Controllers\DocumentController;
use App\Mail\NewRelocationJob;
use App\Models\RealEstateProperty;
use App\Models\Relocation;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\EmlParserController;

use App\Http\Controllers\EgnyteController;
use Illuminate\Support\Facades\Http;

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

Route::get('/', function () {
    return ['PRC API' => app()->version()];
});


Route::get('/test', function(){
    $property = RealEstateProperty::first();
    $signature = hash_hmac('sha256', $property->_id, config('app.key'));
    return "http://127.0.0.1:3000/public/real_estate_share/{$property->_id}?signature={$signature}";
});



/**
 * Eml parser
 */
Route::get('/submit-eml', function() {
    return view('parseEml');
})->name('submit-eml');

Route::get('egnytebrowser', [EgnyteController::class, 'index']);

