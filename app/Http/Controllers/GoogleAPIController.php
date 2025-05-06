<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class GoogleAPIController extends Controller
{
    public function placeDetails(Request $request){
        $request->validate([
            'place_id'
        ]);
        $key = config('services.google_cloud.key');
        
        $data = Http::get('https://maps.googleapis.com/maps/api/place/details/json', [
            'language' => 'it',
            'key' => $key,
            'place_id' => $request->input('place_id')
        ]);
        
        return response()->json(json_decode($data));
    }

    public function geocode(Request $request){
        $request->validate([
            'address'
        ]);
        
        $key = config('services.google_cloud.key');
        $data = Http::get('https://maps.googleapis.com/maps/api/place/autocomplete/json', [
            'components' => 'country:IT',
            'language' => 'it',
            'types' => 'address',
            'key' => $key,
            'input' => $request->input('address')
        ]);
        return response()->json(json_decode($data));
      }
}
