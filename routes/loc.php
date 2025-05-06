<?php

use App\Http\Controllers\Loc\JobController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::group([
    'middleware' => ['api','auth'],
], function($router){
    Route::get('jobs', [JobController::class,'index']);
    Route::get('jobs/{job}', [JobController::class,'show']);
});