<?php

use App\Models\ReloService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PafController;
use App\Http\Controllers\RmcController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BOHomeController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ReloTaskController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GoogleAPIController;
use App\Http\Controllers\ConsultantController;
use App\Http\Controllers\FileUploadController;
use App\Http\Controllers\RelocationController;
use App\Http\Controllers\ReloServiceController;
use App\Http\Controllers\DocumentRequestController;
use App\Http\Controllers\MessageTemplateController;
use App\Http\Controllers\ReloMessageTopicController;
use App\Http\Controllers\RealEstatePropertyController;
use App\Http\Controllers\RealEstateProposalController;
use App\Http\Controllers\Employee\EmployeeTopicController;

use App\Http\Controllers\EmlParserController;

use App\Http\Controllers\EgnyteController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('routes',function(){
    return route('webauthn.store');
});

Route::group([

    'middleware' => 'api',
    'prefix' => 'auth'

], function ($router) {
    Route::get('login', function(){
        abort(401);
    })->name('login');

    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::post('me', [AuthController::class, 'me']);

    Route::post('send_recovery_link', [AuthController::class,'send_recovery_link']);
    Route::post('verify_reset_data', [AuthController::class,'verifyResetData']);
    Route::post('recovery', [AuthController::class,'recovery']);

});


Route::group([
    'middleware' => ['api','auth', 'admin'],
], function($router){

    /**
     * DashBoard
     */
    Route::get('dashboard_widgets', [DashboardController::class, 'getWidget']);

    Route::get('bo_events', [BOHomeController::class,'getEvents']);

    Route::get('relocations', [RelocationController::class, 'index']);
    Route::get('relocations/{id}', [RelocationController::class, 'show']);
    Route::post('relocations', [RelocationController::class, 'store']);
    Route::post('completerelocationservice/{id}', [RelocationController::class, 'completeRelocationService']);
    Route::post('cancelrelocationservice/{id}', [RelocationController::class, 'cancelRelocationService']);
    Route::post('restorerelocationservice/{id}', [RelocationController::class, 'restoreRelocationService']);
    Route::post('addrelocationservice/{id}', [RelocationController::class, 'addService']);
    Route::post('updatena/{id}', [RelocationController::class, 'updateNA']);

    Route::post('update_relocation_responsible/{relocation}', [RelocationController::class,'updateResponsible']);
    
    
    Route::get('companies', [CompanyController::class, 'index']);
    Route::get('companies/{id}', [CompanyController::class, 'show']);
    Route::post('companies', [CompanyController::class, 'store']);
    Route::patch('companies/{id}', [CompanyController::class, 'update']);
    Route::post('disablecompany/{id}', [CompanyController::class, 'disable']);
    Route::post('enablecompany/{id}', [CompanyController::class, 'enable']);

    Route::get('rmcs', [RmcController::class, 'index']);
    Route::get('rmcs/{id}', [RmcController::class, 'show']);
    Route::patch('rmcs/{rmc}', [RmcController::class, 'update']);
    Route::post('rmcs', [RmcController::class, 'store']);
    Route::post('disablermc/{id}', [RmcController::class, 'disable']);
    Route::post('enablermc/{id}', [RmcController::class, 'enable']);

    Route::post('send_new_credential_to_rmc/{rmc}', [RmcController::class, 'sendNewCredential']);

    
    Route::get('employees', [EmployeeController::class, 'index']);
    Route::get('employees/{id}', [EmployeeController::class, 'show']);
    Route::post('employees', [EmployeeController::class, 'store']);
    Route::patch('employees/{id}', [EmployeeController::class, 'update']);
    Route::post('send_new_credential_to_employee/{employee}', [EmployeeController::class, 'sendNewCredential']);
    //Route::delete('employees/{id}', [EmployeeController::class, 'delete']);
    
    
    /**
     * Paf
     * MODULO di VERIFICA IMMOBILE/PROPERTY ASSESSMENT FORM
     * */
    Route::get('paf', [PafController::class, 'index']);
    Route::get('paf/{id}', [PafController::class,'show']);
    Route::get('paf_from_relocation/{id}', [PafController::class,'show2']);
    Route::post('paf', [PafController::class, 'store']);
    Route::patch('paf/{id}', [PafController::class, 'update']);

    /**
     * services
     */
    Route::get('services', [ServiceController::class,'index']);
    Route::get('services/{id}', [ServiceController::class,'show']);
    Route::post('services', [ServiceController::class,'store']);
    Route::patch('services/{id}', [ServiceController::class,'update']);
    
    /**
     * Relo services
     */
    Route::get('reloservices/{id}',[ReloServiceController::class,'show']);
    Route::post('update_relotask_status/{id}',[ReloTaskController::class,'updateStatus']);
    Route::post('update_relotask_description/{task}',[ReloTaskController::class,'updateDescription']);
    Route::post('add_relotask_to_reloservice',[ReloTaskController::class,'addTask']);
    Route::post('attach_consultant_to_reloservice/{service}',[ReloServiceController::class,'attachConsultantToAllTasks']);
    Route::post('plan_relocation_service/{id}', [RelocationController::class, 'planService']);
    Route::post('plan_service_task/{task}', [ReloTaskController::class, 'plan']);
    Route::post('note_service_task/{task}', [ReloTaskController::class, 'note']);
    
    /**
     * files S3
     */
    Route::post('/file/upload', [FileController::class, 'uploadNote']);
	Route::get('/filedownload/{id}', [FileController::class, 'download']);
	Route::post('/filedownload_by_path', [FileController::class, 'downloadByPath']);
	Route::post('/filevalidate/{id}', [FileController::class, 'validateFile']);
	Route::delete('/files/{id}', [FileController::class, 'destroy']);

    /**
     * Doc Request
     */
    Route::post('generate_document_request/{id}',[DocumentRequestController::class,'generate']);
    Route::get('document_request_fill_url/{id}',[DocumentRequestController::class,'getFillUrl']);

    /**
     * Messages
     */
    Route::get('message_topic', [ReloMessageTopicController::class,'index']);
    Route::get('search_message_topic/{relocation}', [ReloMessageTopicController::class,'search']);
    Route::post('create_new_topic/{relocation}', [ReloMessageTopicController::class, 'store']);
    Route::get('message_topic/{topic}', [ReloMessageTopicController::class, 'show']);
    Route::get('download_topic/{topic}', [ReloMessageTopicController::class, 'download']);

    Route::post('store_new_message/{topic}', [ReloMessageTopicController::class, 'newMessage']);
    Route::post('close_topic/{topic}', [ReloMessageTopicController::class, 'close']);

    /**
     * Tasks
    */
    Route::get('tasks', [ReloTaskController::class,'index']);
    ROute::post('attach_consultant_to_relotask/{task}', [ReloTaskController::class, 'attachConsultant']);

    /**
     * Consultants
     */
    Route::get('consultants', [ConsultantController::class,'index']);
    Route::get('consultants/{consultant}', [ConsultantController::class,'show']);
    Route::post('consultants', [ConsultantController::class,'store']);
    Route::patch('consultants/{consultant}', [ConsultantController::class,'update']);

    /**
     * Templates
    */
    Route::get('templates/messages', [MessageTemplateController::class, 'index']);
    Route::get('templates/messages/{template}', [MessageTemplateController::class, 'show']);
    Route::patch('templates/messages/{template}', [MessageTemplateController::class, 'update']);
    Route::post('templates/messages', [MessageTemplateController::class, 'store']);

    /**
     * RE Proposals
     */
    Route::post('real_estate_proposal', [RealEstateProposalController::class, 'store']);
    Route::post('cancel_real_estate_proposal/{rep}', [RealEstateProposalController::class, 'delete']);
    Route::post('select_real_estate_proposal/{rep}', [RealEstateProposalController::class, 'select']);//
    Route::post('send_real_estate_proposal/{rep}', [RealEstateProposalController::class, 'send']);
    Route::post('update_real_estate_proposal_rating/{rep}', [RealEstateProposalController::class, 'updateRating']);

    /**
     * RE Properties
     */
    Route::get('real_estate_property', [RealEstatePropertyController::class,'index']);
    Route::get('real_estate_property/{property}', [RealEstatePropertyController::class,'show']);
    Route::post('real_estate_property', [RealEstatePropertyController::class,'store']);
    Route::patch('real_estate_property/{property}', [RealEstatePropertyController::class,'update']);
    Route::delete('real_estate_property/{property}', [RealEstatePropertyController::class,'delete']);
    
    Route::post('attach_real_estate_property_image/{property}', [RealEstatePropertyController::class,'attachPhoto']);
    Route::delete('delete_real_estate_property_photo/{photo}', [RealEstatePropertyController::class,'deletePhoto']);

    /**
     * Geocoding
     */
    Route::post('/gmap_api/geocode', [GoogleAPIController::class,'geocode']);
    Route::post('/gmap_api/place_details', [GoogleAPIController::class,'placeDetails']);

    /**
     * Users
     */
    Route::get('/users', [UserController::class,'index'] );
    
    
    /**
    * egnyte
    */
    Route::get('egnyte', [FileUploadController::class, 'index']);
    Route::post('egnyte', [FileUploadController::class, 'store']);

    Route::post('update_relocation_egnyte/{relocation}', [RelocationController::class,'updateEgnyte']);
    
    Route::post('/parse-eml', [EmlParserController::class, 'parseEml'])->name('parse-eml');

    Route::get('/egnyte-directory', function () {

        $path = "/Shared/Media Era/";
        $url = "https://principal.egnyte.com/pubapi/v1/fs{$path}?list_content=true&allowed_link_types=false&count=0&offset=0&sort_by=last_modified&key&sort_direction=descending&perms&include_perm&list_custom_metadata";

        $response = Http::withHeaders([
            'Authorization' => 'Bearer awzd3jyav5tj97ax2vacf5np',
            'Cookie' => 'recentWg=principal'
        ])->get($url);

        return $response->json();
    });

    Route::get('/egnyte-directory/{path}', function ($path) {
        // $path = "/Shared/Media Era/";
        $path = base64_decode($path);
        $url = "https://principal.egnyte.com/pubapi/v1/fs{$path}?list_content=true&allowed_link_types=false&count=0&offset=0&sort_by=last_modified&key&sort_direction=descending&perms&include_perm&list_custom_metadata";

        $response = Http::withHeaders([
            'Authorization' => 'Bearer awzd3jyav5tj97ax2vacf5np',
            'Cookie' => 'recentWg=principal'
        ])->get($url);

        return $response->json();
    });

    Route::get('/egnyte-file-base64/{path_base64}/{entry_id}', function ($path_base64, $entry_id) {
        // $path = "/Shared/Media Era/Malta - Home Search Procedure Updated 18_05_2022.eml";
        // $entry_id = "c0e884d2-f25e-45b9-bf2c-1b19dc900246";
        $path = base64_decode($path_base64);

        if (pathinfo(strtolower($path),PATHINFO_EXTENSION )==='eml') {
            $url = "https://principal.egnyte.com/pubapi/v1/fs-content/{$path}?entry_id={$entry_id}";

            $response = Http::withHeaders([
                'Authorization' => 'Bearer awzd3jyav5tj97ax2vacf5np',
                'Cookie' => 'recentWg=principal'
            ])->get($url);
        
            return response( ["file64" => base64_encode( $response->body() ) ] )
                ->header('Content-Type', 'application/json');
        } else {
            return response( ["file64" => null ] )
                ->header('Content-Type', 'application/json');
        }
    });

});



Route::group([
    'middleware' => ['api'],
    'prefix' => 'public'
], function($router){
    Route::get('document_request/{id}', [DocumentRequestController::class,'show']);
    Route::post('complete_document_request/{id}', [DocumentRequestController::class,'complete']);
    Route::get('real_estate_share/{id}', [RealEstatePropertyController::class,'showPublic']);
    
});




// Route::post('parse-eml', [EmlParserController::class, 'parseEml'])->name('parse-eml');
Route::get('/prova/{id}', function (string $id) {
    return 'prova '.$id;
});

Route::get('/egnyte-folder-id/{folder_id}', function ($folder_id) {

    // $path = "/Shared/Media Era/";
    // $path = base64_decode($path);
    $path = $folder_id;

    $url = "https://principal.egnyte.com/pubapi/v1/fs/ids/folder/{$path}?list_content=true&allowed_link_types=false&count=0&offset=0&sort_by=last_modified&key&sort_direction=descending&perms&include_perm&list_custom_metadata";

    $response = Http::withHeaders([
        'Authorization' => 'Bearer awzd3jyav5tj97ax2vacf5np',
        'Cookie' => 'recentWg=principal'
    ])->get($url);

    return $response->json();
});

Route::get('/egnyte-download/{path_base64}/{entry_id}', function ($path_base64, $entry_id) {
    /*
    $path = "/Shared/Media Era/Malta - Home Search Procedure Updated 18_05_2022.eml";
    $entry_id = "c0e884d2-f25e-45b9-bf2c-1b19dc900246";
    */
    $path = base64_decode($path_base64);

    $url = "https://principal.egnyte.com/pubapi/v1/fs-content/{$path}?entry_id={$entry_id}";

    $response = Http::withHeaders([
        'Authorization' => 'Bearer awzd3jyav5tj97ax2vacf5np',
        'Cookie' => 'recentWg=principal'
    ])->get($url);

    // dd($response->headers()['content-disposition'][0]);
    return response($response->body(), $response->status())
        ->header('Content-Type', 'application/octetstream')
        ->header('Content-Transfer-Encoding', 'Binary')
        ->header('Content-disposition', $response->headers()['content-disposition'][0] );
});

