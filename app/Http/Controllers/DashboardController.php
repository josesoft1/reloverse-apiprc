<?php

namespace App\Http\Controllers;

use App\Models\ReloMessage;
use App\Models\ReloMessageTopic;
use App\Models\ReloTask;
use App\Models\File;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function getWidget(Request $request){
        $waiting_topic = ReloMessageTopic::where('waiting', true)
            ->whereNull('closed_at')
            ->whereHas('messages', function($q){
                $q->where(function($q2){
                    $q2->whereNotNull('user_id');
                    $q2->orWhereNotNull('employee_id');
                });
            })
            ->with([
                'messages'=>function($q){
                    $q->where(function($q2){
                        $q2->whereNotNull('user_id');
                        $q2->orWhereNotNull('employee_id');
                    });
                    $q->orderBy('created_at','desc')->take(1);
                },
                'messages.employee' => function($q){
                    $q->select(['_id','name','surname','email']);
                },
                'messages.user' => function($q){
                    $q->select(['_id','name','surname','email']);
                }])->take(10)->get();

        $waiting_topic = $waiting_topic->filter(function($el){
            return $el->messages->count() > 0;
        });

        $waiting_topic = $waiting_topic->map(function($el){
            $el->author = (!empty($el->messages[0]->author)) ? $el->messages[0]->author : '';
            return $el;
        });
        if(!empty($request->input('waitingtopic_order_by'))){
            switch($request->waitingtopic_order_by){
                case 'datetime':
                    $waiting_topic = $waiting_topic->sortByDesc('created_at');
                    break;
                case 'customer':
                    $waiting_topic = $waiting_topic->sortBy('author');
                    break;
            }
        }

        $today_tasks_count = ReloTask::where('date','>=',now()->startOfDay())->where('date','<=',now()->endOfDay())->count();
        
        $unvalidated_files = File::where(function($q){
            $q->where('status',0);
            $q->orWhereNull('status');
        })->with(['relocation'])->whereNotNull('relocation_id')->take(30)->get(); 

        return response()->json([
            'waiting_topic_count'=>$waiting_topic->count(),
            'waiting_topic'=>$waiting_topic,
            'today_tasks_count' => $today_tasks_count,
            'unvalidated_files' => $unvalidated_files
        ]);
    }
}
