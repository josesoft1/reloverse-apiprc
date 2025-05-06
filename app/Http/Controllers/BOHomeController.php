<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Relocation;
use App\Models\ReloService;
use App\Models\ReloTask;
use App\Models\Service;
use Carbon\Carbon;

class BOHomeController extends Controller
{
    public function getEvents(Request $request){
        $from = Carbon::parse($request->from);
        $to = Carbon::parse($request->to);

        //$relos = ReloService::where('date','>=',$from)->where('date','<=',$to)->with(['relocation.employee'])->get();
        $tasks = ReloTask::where('date', '>=',$from)->where('date','<=',$to)->with(['service.relocation.employee','consultant'])->get();
        return $tasks;
    }
}
