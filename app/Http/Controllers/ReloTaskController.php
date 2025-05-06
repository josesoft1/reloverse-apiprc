<?php

namespace App\Http\Controllers;

use App\Models\ReloTask;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReloTaskController extends Controller
{
    public function updateStatus(Request $request, $id){
        $task = ReloTask::findOrFail($id);
        $task->status = (int)$request->input('new_status');
        $task->save();
    }

    public function updateDescription(Request $request, ReloTask $task){
        $request->validate(['description' => 'required']);

        $task->name = $request->description;
        $task->save();
        return response()->json(['status'=>'OK']);
    }

    public function addTask(Request $request){
        $request->validate(['task.name' => 'required', 'service_id' => 'required']);

        $task = new ReloTask($request->input('task'));
        $task->service_id = $request->input('service_id');
        $task->status = 0;
        $task->consultant_id = null;
        $task->save();

        return response()->json(['task'=>$task]);

    }

    public function attachConsultant(Request $request, ReloTask $task){
        $task->consultant_id = $request->input('consultant_id');
        $task->unnecessary = $request->input('unnecessary', false) == 'true';
        $task->save();
        return $task;
    }

    public function index(Request $request){
        $tasks = ReloTask::whereIn('status',[0,1])
            ->with([
            'service' => function($q){$q->select(['_id','name','relocation_id']);},
            'service.relocation' => function($q){$q->select(['_id','job','employee_id']);},
            'service.relocation.employee' => function($q){$q->select(['_id','name','surname','company_id']);},
            //'service.relocation.employee.company'
        ])->orderBy('date','asc');

        if(!empty($request->input('search'))){
            $tasks->where(function($q) use ($request){
                $q->where('name', 'LIKE', '%'.$request->search.'%');
                $q->orWhereHas('service.relocation', function($q2) use ($request){
                    $q2->where('job', 'LIKE', '%'.$request->search.'%');
                });
                $q->orWhereHas('service.relocation.employee', function($q2) use ($request){
                    $q2->where('name', 'LIKE', '%'.$request->search.'%');
                    $q2->orWhere('surname', 'LIKE', '%'.$request->search.'%');
                });
                $q->orWhereHas('service.relocation.employee.company', function($q2) use ($request){
                    $q2->where('name', 'LIKE', '%'.$request->search.'%');
                });
            });
        }
        
        /*
        $tasks = $tasks->merge(ReloTask::whereIn('status',[0,1])
            ->whereNull('date')
            ->with([
                'service' => function($q){$q->select(['_id','name','relocation_id']);},
                'service.relocation' => function($q){$q->select(['_id','job','employee_id']);},
                'service.relocation.employee' => function($q){$q->select(['_id','name','surname','company_id']);},
                //'service.relocation.employee.company'
            ])->get());
        */
        return $tasks->paginate($request->input('per_page',10));
    }

    public function plan(Request $request, ReloTask $task){
        $task->planned_by_authority = $request->input('planned_by_authority') == 'true';
        $task->confirmed_by_customer = $request->input('confirmed_by_customer') == 'true';
        $task->date = Carbon::parse($request->input('date').' '.$request->input('time'),'Europe/Rome');
        $task->save();
    }

    public function note(Request $request, ReloTask $task){
        $task->note = $request->input('note');
        $task->save();
    }
}
