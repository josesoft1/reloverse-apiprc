<?php

namespace App\Http\Controllers;

use App\Models\ReloService;
use Illuminate\Http\Request;

class ReloServiceController extends Controller
{
    public function show(Request $request, $id){
        return ReloService::with(['tasks', 'tasks.consultant' => function($q){$q->select(['_id','name','surname']);}])->findOrFail($id);
    }

    public function attachConsultantToAllTasks(Request $request, ReloService $service){
        $request->validate(['consultant_id'=>'required']);
        
        foreach($service->tasks as $task){
            $task->consultant_id = $request->input('consultant_id');
            $task->save();
        }
    }
}
