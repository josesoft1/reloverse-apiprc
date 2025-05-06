<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Relocation;
use Illuminate\Http\Request;

class EmployeeJobController extends Controller
{
    public function index(Request $request){
        $jobs = Relocation::where('employee_id', auth()->user()->employee_id)->get();

        return response()->json($jobs);
    }

    public function show(Request $request, $id){
        return Relocation::with(['services.tasks'])->where('employee_id', auth()->user()->employee_id)->where('_id',$id)->firstOrFail();
    }
}
