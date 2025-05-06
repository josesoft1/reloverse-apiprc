<?php

namespace App\Http\Controllers\Rmc;

use App\Http\Controllers\Controller;
use App\Models\Relocation;
use Illuminate\Http\Request;

class JobController extends Controller
{
    public function index(Request $request){
        $jobs = Relocation::with([
            'employee' => function($q){$q->select(['_id','name','surname','company_id']);},
            'employee.company' => function($q){$q->select(['_id','name']);},
            'services' => function($q){$q->select(['_id','relocation_id','service_id']);},
            'services.tasks' => function($q){$q->select(['_id','service_id','name','date','status','consultant_id']);},
            'services.tasks.consultant' => function($q){$q->select(['_id','name','surname']);},
        ])->whereHas('employee', function($q){
            $q->whereHas('company', function($q2){
                $q2->where('rmc_id', auth()->user()->rmc_id);
            });
        })->orderBy('created_at','desc');
        if(!empty($request->input('search'))){
            $jobs->where(function($q) use ($request){
                $q->where('job', 'LIKE', '%'.$request->search.'%');
                $q->orWhereHas('employee', function($q2) use ($request){
                    $q2->where('name', 'LIKE', '%'.$request->search.'%');
                    $q2->orWhere('surname', 'LIKE', '%'.$request->search.'%');
                });
            });
        }
        return $jobs->paginate($request->input('per_page',15));
    }

    public function show(Request $request, Relocation $job){
        if($job->employee->company->rmc_id != auth()->user()->rmc_id){
            abort(403);
        }
        $job->load([
            'employee' => function($q){$q->select(['_id','name','surname','company_id']);},
            'employee.company' => function($q){$q->select(['_id','name']);},
            'services' => function($q){$q->select(['_id','relocation_id','service_id', 'name']);},
            'services.tasks' => function($q){$q->select(['_id','service_id','name','date','status','consultant_id'])->whereNotNull('name');},
            'services.tasks.consultant' => function($q){$q->select(['_id','name','surname']);},
        ]);
        return $job;
    }
}
