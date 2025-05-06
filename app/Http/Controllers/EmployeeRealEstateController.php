<?php

namespace App\Http\Controllers;

use App\Models\EmployeeRealEstateProposal;
use App\Models\RealEstateProposal;
use App\Models\Relocation;
use App\Models\ReloMessage;
use App\Models\ReloMessageTopic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class EmployeeRealEstateController extends Controller
{
    public function storeEmployeeRELink(Request $request){
        $request->validate(['share_url' => 'required']);
        $erepro = new EmployeeRealEstateProposal();

        $erepro->link = $request->input('share_url');
        $erepro->employee_id = auth()->user()->employee_id;

        $erepro->save();
        
        $relocations = Relocation::where('employee_id', $erepro->employee_id)->whereIn('status',[Relocation::STATUS_CREATED,Relocation::STATUS_WORKING])->get(); 
        if(count($relocations) == 0){            
            Mail::send([],[], function ($message) use ($erepro){
                $message->to('support@mediacrm.it')
                ->from('noreply@mediacrm.it')
                ->subject("New Real Estate proposal received from customer")
                ->html('The employee '.auth()->user()->full_name.' ('.auth()->user()->employee->email.') has just sent a real estate proposal link/s but no associable Job was found. Here the content:<br/>'.nl2br($erepro->link));
            });
        }else{
            foreach($relocations as $relocation){
                $erepro->relocation_id = $relocation->_id;
                $erepro->save();

                $relo_topic = ReloMessageTopic::where('relocation_id', $relocation->_id)->where('topic','Home Search')->firstOrNew();
                $relo_topic->topic = 'Home Search';
                $relo_topic->relocation_id = $relocation->_id;
                $relo_topic->waiting = true;
                $relo_topic->waiting_employee = false;
                $relo_topic->status = 0;
                $relo_topic->save();

                $relo_message = new ReloMessage();
                $relo_message->relo_message_topic_id = $relo_topic->_id;
                $relo_message->relocation_id = $erepro->relocation_id;
                $relo_message->user_id = auth()->user()->_id;
                $relo_message->content = "Automatic message: <br/> I found a new real estate property for our relocation project. Please consider this property:<br/>{$erepro->link}";
                $relo_message->save();
            }
        }

        return $erepro;
    }

    public function indexProposal(Request $request){

        $reps = RealEstateProposal::whereNull('employee_rating')
        ->has('property')
        ->whereHas('relocation', function($q){
            $q->where('employee_id', auth()->user()->employee_id);
        })->with(['relocation'=>function($q){
            $q->select(['_id','job','from','to','created_at','updated_at','deleted_at']);
        },'property', ])->get();

        return $reps;
    }

    public function sendProposalFeedback(Request $request, RealEstateProposal $rep){
        abort_if($rep->relocation->employee_id != auth()->user()->employee_id,403,"User is not authorized");

        $relo_topic = ReloMessageTopic::where('relocation_id', $rep->relocation_id)->where('topic','Home Search')->firstOrNew();
        $relo_topic->topic = 'Home Search';
        $relo_topic->relocation_id = $rep->relocation_id;
        $relo_topic->waiting = true;
        $relo_topic->waiting_employee = false;
        $relo_topic->status = 0;
        $relo_topic->save();
        
        $rep->employee_rating = (int)$request->input('rating');
        $rep->employee_feedback = $request->input('feedback');
        $rep->save();

        $relo_message = new ReloMessage();
        $relo_message->relo_message_topic_id = $relo_topic->_id;
        $relo_message->relocation_id = $rep->relocation_id;
        $relo_message->user_id = auth()->user()->_id;
        $relo_message->content = "My rating about the proposal <strong> {$rep->property->title} </strong>(id: {$rep->property->_id}) is <br/><strong>{$rep->employee_rating}/5</strong><br/><br/><strong>Feedback:</strong> <br/>".nl2br($rep->employee_feedback);
        $relo_message->save();

        
    }

    public function show(Request $request, RealEstateProposal $rep){

        abort_if($rep->relocation->employee_id != auth()->user()->employee_id, 403, "User is not authorized");

        $rep->load(['property', 'property.photos']);
        if(!empty($rep->property->photos)){
            $rep->property->thumbnail_images = $rep->property->photos->map(function($e, $i){
                //return Storage::disk('s3')->url($e->path);
                if(empty($e->thumb_300_path)){
                    return null;
                }
                if($i == 0){
                    return Storage::disk('s3')->url($e->thumb_300_path);
                }else{
                    return Storage::disk('s3')->url($e->thumb_80_path);
                }
            });
            $rep->property->images = $rep->property->photos->map(function($e){
                return Storage::disk('s3')->url($e->path);
            });
        }

        return $rep;
    }
}
