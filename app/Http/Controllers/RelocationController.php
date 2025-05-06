<?php

namespace App\Http\Controllers;

use App\Mail\NewRelocationJob;
use App\Models\Company;
use App\Models\Employee;
use Illuminate\Http\Request;
use App\Models\Relocation;
use App\Models\ReloMessage;
use App\Models\ReloService;
use App\Models\ReloTask;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class RelocationController extends Controller
{
    public function store(Request $request){
        $request->validate([
            'company._id' => 'required',
            'employee._id' => 'required'
        ]);
        $company = Company::findOrFail($request->input('company._id'));
        $employee = Employee::findOrFail($request->input('employee._id'));
        
        $last_relocation = Relocation::orderBy('created_at','desc')->first();
        
        if(!empty($last_relocation)){
            $job = (int)str_replace("J", "", $last_relocation->job);
            $job = $job+1;
            $job = "J".str_pad($job,4,0,STR_PAD_LEFT);
        }else{
            $job = "J1000";
        }

        $relocation = new Relocation();
        $relocation->company_id = $company->_id;
        $relocation->employee_id = $employee->_id;
        $relocation->from = $request->input('from');
        $relocation->from_city = $request->input('from_city');
        $relocation->to = $request->input('to');
        $relocation->to_city = $request->input('to_city');
        $relocation->job = $job;
        $relocation->status = Relocation::STATUS_CREATED;
        $relocation->responsible_id = auth()->user()->_id;
        $relocation->save();

        $rmessage = new ReloMessage();
        $rmessage->to = $relocation->employee->email;
        $rmessage->subject = "New relocation JOB created";
        $rmessage->relocation_id = $relocation->_id;
        $rmessage->save();
        
        Mail::to($relocation->employee->email)->queue(new NewRelocationJob($relocation));
    }
    
    public function index(Request $request){
        $relocations = Relocation::with([
            'company' => function($q){$q->select(['_id','name']);},
            'employee' => function($q){$q->select(['_id','name', 'surname','company_id']);}
        ])->orderBy('created_at','desc');

        if(!empty($request->input('search'))){
            $relocations->where(function($q) use ($request){
                $q->where('job', 'LIKE', '%'.$request->search.'%');
                $q->orWhereHas('employee', function($q2) use ($request){
                    $q2->where('name', 'LIKE', '%'.$request->search.'%');
                    $q2->orWhere('surname', 'LIKE', '%'.$request->search.'%');
                });
                $q->orWhereHas('employee.company', function($q2) use ($request){
                    $q2->where('name', 'LIKE', '%'.$request->search.'%');
                });
            });
        }
        if(!empty($request->input('show_only_mine')) && filter_var($request->input('show_only_mine',false),FILTER_VALIDATE_BOOLEAN) == true){
            $relocations->where('responsible_id', auth()->user()->_id);
        }

        return $relocations->paginate();
    }

    public function show(Request $request, $id){
        return Relocation::with([
            'company',
            'employee',
            'files',
            'services.tasks',
            'documentRequests'=>function($q){$q->whereNull('completed_at');},
            'topics'=>function($q){$q->orderBy('updated_at','desc');},
            'realEstateProposals' => function($q){$q->orderBy('created_at','desc');},
            'realEstateProposals.property',
            'responsible'
        ])->findOrFail($id);
    }

    public function cancelRelocationService(Request $request, $id){
        $request->validate(['index'=>'required|gte:0']);
        $relocation = Relocation::with(['company', 'employee', 'files'])->findOrFail($id);
        $service = $relocation->services[$request->input('index')];
        $service['status'] = 3;
        $service['canceled_at'] = now();
        foreach($service->tasks as $task){
            $task->delete();
        }
        $service->delete();
        $relocation->save();
        return $relocation;
    }

    public function completeRelocationService(Request $request, $id){
        $request->validate(['index'=>'required|gte:0']);
        $relocation = Relocation::with(['company', 'employee', 'files'])->findOrFail($id);
        $service = $relocation->services[$request->input('index')];
        $service['status'] = 2;
        $service['canceled_at'] = null;
        $service['completed_at'] = now();
        $service->save();
        $relocation->save();
        return $relocation;
    }

    public function restoreRelocationService(Request $request, $id){
        $request->validate(['index'=>'required|gte:0']);
        $relocation = Relocation::with(['company', 'employee', 'files'])->findOrFail($id);
        $service = $relocation->services[$request->input('index')];
        $service['status'] = 0;
        $service['canceled_at'] = null;
        $service->save();
        $relocation->save();
        return $relocation;
    }

    public function addService(Request $request, $id){
        $request->validate(['service.name'=>'required','service.plannable'=>'required']);
        $relocation = Relocation::with(['company', 'employee', 'files'])->findOrFail($id);
        $service = new ReloService($request->input('service'));
        $service->relocation_id = $relocation->_id;
        $service->save();

        foreach ($request->input('tasks',[]) as $task) {
            $rtask = new ReloTask();
            $rtask->name = $task['name'];
            $rtask->service_id = $service->_id;
            $rtask->status = 0;
            $rtask->consultant_id = null;
            $rtask->save();
        }

        $relocation->status = Relocation::STATUS_WORKING;
        $relocation->save();
    }

    public function updateNA(Request $request, $id){
        $relocation = Relocation::findOrFail($id);
        $relocation->na = $request->input('na',json_decode("{}"));
        $relocation->save();
    }

    public function planService(Request $request, $id){
        $relocation = Relocation::findOrFail($id);
        $service = $relocation->services()->where('_id',$request->input('service_id'))->firstOrFail();
        $service->date = Carbon::parse($request->input('date').' '.$request->input('time'),'Europe/Rome');
        $service->confirmed_by_customer = $request->input('confirmed_by_customer',false);
        $service->save();
        $relocation->services()->save($service);
    }

    public function updateResponsible(Request $request, Relocation $relocation){
        $request->validate(['responsible_id'=>'required']);

        $relocation->responsible_id = $request->input('responsible_id');
        $relocation->save();
        return $relocation;
    }

    /**
     * update egnyte url (Egnyte folder)
     * */
    public function updateEgnyte(Request $request, Relocation $relocation){
        $request->validate(['egnyte_url'=>'required']);

        $stringa = $request->input('egnyte_url');
        if (substr($stringa, -1) != '/') {
            $stringa.='/';
        }
        $relocation->egnyte_url = $stringa;

        $stringa_2pezzi = explode('#storage/files/1', $stringa);
        $relocation->egnyte_dir = $stringa_2pezzi[1];

        $relocation->save();
        return $relocation;
    }
}
