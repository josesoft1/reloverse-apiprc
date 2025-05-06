<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\DocumentRequest;
use Illuminate\Http\Request;

class EmployeeDocumentRequestController extends Controller
{
    public function index(Request $request){
        
        return DocumentRequest::with(['relocation' => function($q){
            $q->select(['_id','job','created_at','updated_at', 'deleted_at']);
        }])
        ->whereHas('relocation',function($q){
            $q->where('employee_id', auth()->user()->employee->_id);
        })
        ->whereNull('completed_at')
        ->get()->map(function($e){
            $e->document_url = $e->generateUrl();
            return $e;
        });
    }
}
