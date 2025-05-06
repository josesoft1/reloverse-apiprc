<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ReloMessageTopic as Topic;
use App\Models\ReloMessage;
use App\Models\User;

class EmployeeTopicController extends Controller
{
    public function index(Request $request){
        return Topic::whereHas('relocation',function($q){
            $q->where('employee_id', auth()->user()->employee_id);
        })->paginate(1000);
    }

    public function show(Request $request, $id){
        return Topic::with(['messages'=>function($q){
                    $q->orderBy('date','asc');
                }])
                ->whereHas('relocation',function($q){
                    $q->where('employee_id', auth()->user()->employee_id);
                })->find($id);
    }

    public function storeMessage(Request $request, Topic $topic){
        $request->validate(['content'=>'required']);
        $content = $request->input('content');

        if(auth()->user()->role != User::ROLE_EMPLOYEE){
            abort(403);
        }

        if($topic->relocation->employee_id !== auth()->user()->employee->_id){
            abort(403);
        }

        $topic->load(['relocation']);

        if(empty($topic->relocation)){
            abort(500, 'The relocation doesn\t exists anymore.');
        }

        $message = new ReloMessage();        
        $message->content = $content;
        $message->relocation_id = $topic->relocation->_id;
        $message->relo_message_topic_id = $topic->_id;
        $message->employee_id = $topic->relocation->employee->_id;

        $message->save();
        $message->topic->waiting_employee = false;
        $message->topic->waiting = true;
        $message->topic->save();

    }
}
