<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FileUpload;


class FileUploadController extends Controller
{
    //
    public function index(Request $request){
        $pafs = FileUpload::query();


        if(!empty($request->input('search'))){
            $pafs->where(function($q) use($request){
                $q->where('tenant_name','LIKE','%'.$request->input('search').'%');
                $q->orWhere('tenant_lastname','LIKE','%'.$request->input('search').'%');
                $q->orWhere('tenant_address','LIKE','%'.$request->input('search').'%');
            });
        }

        return $pafs->paginate($request->input('per_page','20'));
    }


    public function store(Request $request){
        /*
        include( base_path().'/vendor/egnyte/class/curl.php');
        include( base_path().'/vendor/egnyte/class/curl_response.php');
        include( base_path().'/vendor/egnyte/class/EgnyteClient.php');
        */

        $domain = 'principal';
        $oauthToken = 'awzd3jyav5tj97ax2vacf5np';
        $folder = '';

        if ($request->has('egnyte_dir')) {
            $folder = $request->input('egnyte_dir');

            // $folder = '/Shared/Media Era/';
            // $folder = str_replace('%20',' ', "/Shared/Relocation/Operations/pmassaro/PROVIDERS-RMCs/Irish%20Relocation/");
            // $folder = '/Private/mediaera/';
            // $folder = rawurldecode("/Shared/Relocation/Operations/pmassaro/PROVIDERS-RMCs/Irish%20Relocation/");
        }else {
            // $folder = base64_encode('/Shared/Media Era/');
            $folder = '/Shared/Media Era/prova url2/Mario Rossi/Codice prova/';
        }

        /*
        if($request->has('file')){
            $base = hash_hmac('sha256', $request->item_id, config('app.key'));      
            $file->path = $request->file->store('files/'. $request->item_type .'/'. $base, 's3');
            $file->extension = $request->file->extension();
            $file->name = $request->file->getClientOriginalName();
            $file->size = $request->file->getSize();
            $file->mime = $request->file->getMimeType();
        }
        */
        if($request->file()) {
            //$fileName = $_FILES['filedata']['name'];
            $file_name = date('YmdHis', time()).'_'.$request->file->getClientOriginalName();
            // $file_name = $request->file->getClientOriginalName();
            /*
            $file_path = $request->file('file')->storeAs('uploads', $file_name, 'public');
            $fileUpload->name = time().'_'.$request->file->getClientOriginalName();
            $fileUpload->path = '/storage/' . $file_path;
            $fileUpload->save();
            */
            // return response()->json(['success'=>'File uploaded successfully.']);


            // $fileBinaryContents = file_get_contents($_FILES['filedata']['tmp_name']);
            $fileBinaryContents = file_get_contents($request->file->path());
            

            $egnyte = new \EgnyteClient($domain, $oauthToken);
            $resp = $egnyte->uploadFile($folder, $file_name, $fileBinaryContents);

            // $resp = $egnyte->uploadFile( $egnyte->encodePath( ($folder) ), $file_name, $fileBinaryContents);

            // return $resp;
            // return response()->json(['success'=>'File uploaded successfully.']);
            
            
            // return response()->json(['success'=> $resp->statusCode, 'path'=> ($folder), 'path2'=> $request->file->path() ]);
            return response()->json(['success'=> $resp->statusCode ]);
            /*
            
            {
    "success": {
        "curlResponse": {
            "body": "{\"checksum\":\"2ffabf53ae14937dc35654683a2f6257187c1542fe28d9ca16dfdf6d5866559f577b44b5c178c7da22b0378aadb6bfa00edce27777e768d54ba2d5b60806010b\",
                \"path\":\"/Private/mediaera/20240416152141_Documento_di_prova.pdf\",
                \"group_id\":\"6ff95bd6-5dfe-4c58-97cb-60cc3cd6c786\",
                \"entry_id\":\"8b0c7efa-ff6d-4f7d-986b-c41608092fa4\"}",
            "headers": {
                "Http-Version": "1.1",
                "Status-Code": "200",
                "Status": "200 ",
                "content-type": "application/json;charset=UTF-8",
                "transfer-encoding": "chunked",
                "x-mashery-responder": "am2-gcp-mashery443-pasv-slave03",
                "x-accesstoken-qps-allotted": "2",
                "x-accesstoken-qps-current": "1",
                "x-accesstoken-quota-allotted": "1000",
                "x-accesstoken-quota-current": "69",
                "x-quota-reset": "Wednesday, April 17, 2024 12:00:00 AM GMT",
                "etag": "8b0c7efa-ff6d-4f7d-986b-c41608092fa4",
                "x-sha512-checksum": "2ffabf53ae14937dc35654683a2f6257187c1542fe28d9ca16dfdf6d5866559f577b44b5c178c7da22b0378aadb6bfa00edce27777e768d54ba2d5b60806010b",
                "last-modified": "Tue, 16 Apr 2024 13:21:41 GMT",
                "date": "Tue, 16 Apr 2024 13:21:41 GMT",
                "x-content-type-options": "nosniff",
                "x-xss-protection": "1; mode=block",
                "strict-transport-security": "max-age=31536000; includeSubDomains",
                "x-egnyte-request-id": "513892F5:3C47_A2D8FBE7:01BB_--_34E57D|am2-gcp-l1webui-c56n+http_l1_webui"
            }
        },
        "statusCode": 200,
        "body": "{\"checksum\":\"2ffabf53ae14937dc35654683a2f6257187c1542fe28d9ca16dfdf6d5866559f577b44b5c178c7da22b0378aadb6bfa00edce27777e768d54ba2d5b60806010b\",\"path\":\"/Private/mediaera/20240416152141_Documento_di_prova.pdf\",\"group_id\":\"6ff95bd6-5dfe-4c58-97cb-60cc3cd6c786\",\"entry_id\":\"8b0c7efa-ff6d-4f7d-986b-c41608092fa4\"}",
        "errorMap": {
            "400": "Bad Request",
            "401": "Unauthorized",
            "403": "Forbidden",
            "404": "Not Found",
            "415": "Unsupported Media Type",
            "500": "Internal Server Error",
            "502": "Bad Gateway",
            "503": "Service Unavailable",
            "596": "Service Not Found"
        }
    }
}
            
            */
        }

    }

}
