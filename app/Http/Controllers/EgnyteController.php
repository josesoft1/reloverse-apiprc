<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;

class EgnyteController extends Controller
{
    //
    public function index()
    {
        $path = "/Shared/Media Era/";

        $client = new Client([
            'base_uri' => 'https://principal.egnyte.com/pubapi/v1/',
            'timeout'  => 2.0,
            'headers' => [
                'Authorization' => 'Bearer awzd3jyav5tj97ax2vacf5np',
                'Cookie' => 'recentWg=principal'
            ]
        ]);

        try {

            $response = $client->request('GET', 'fs' . $path, [
                'query' => [
                    'list_content' => 'true',
                    'allowed_link_types' => 'false',
                    'count' => '0',
                    'offset' => '0',
                    'sort_by' => 'last_modified',
                    'key' => '',
                    'sort_direction' => 'descending',
                    'perms' => '',
                    'include_perm' => '',
                    'list_custom_metadata' => ''
                ]
            ]);

            $data = json_decode($response->getBody(), true);

            return view('egnytebrowser', [
                'path' => $path,
                'folders' => $data['folders'] ?? [],
                'files' => $data['files'] ?? []
            ]);

        } catch (\Exception $e) {

            return view('egnytebrowsererror', ['error' => $e->getMessage()]);
            
        }
    }
}
