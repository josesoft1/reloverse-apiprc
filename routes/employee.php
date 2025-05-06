<?php

use App\Http\Controllers\Employee\EmployeeDocumentRequestController;
use App\Http\Controllers\Employee\EmployeeJobController;
use App\Http\Controllers\Employee\EmployeeTopicController;
use App\Http\Controllers\EmployeeRealEstateController;
use App\Models\EmployeeRealEstateProposal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::group([
    'middleware' => ['api','auth'],
], function($router){
    Route::get('jobs', [EmployeeJobController::class,'index']);
    Route::get('jobs/{id}', [EmployeeJobController::class,'show']);
    Route::get('document_requests', [EmployeeDocumentRequestController::class,'index']);
    Route::get('topics', [EmployeeTopicController::class, 'index']);
    Route::get('topics/{id}', [EmployeeTopicController::class, 'show']);
    Route::post('topics/{topic}/new_message',  [EmployeeTopicController::class, 'storeMessage']);

    Route::get('re_proposals', [EmployeeRealEstateController::class,'indexProposal']);
    Route::get('re_proposals/{rep}', [EmployeeRealEstateController::class,'show']);
    Route::post('send_re_link_to_backoffice', [EmployeeRealEstateController::class, 'storeEmployeeRELink']);
    Route::post('send_re_proposal_feeback/{rep}', [EmployeeRealEstateController::class, 'sendProposalFeedback']);
});