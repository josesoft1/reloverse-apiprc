<?php

namespace App\Http\Controllers;

use App\Mail\NewDocRequiredMail;
use App\Models\DocumentRequest;
use App\Models\File;
use App\Models\Relocation;
use App\Models\ReloMessage;
use App\Models\ReloMessageTopic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class DocumentRequestController extends Controller
{
    public function complete(Request $request, $id){
        $request->validate(['signature'=>'required']);
        $signature = hash_hmac('sha256', $id, config('app.key'));

        try{
            if($request->input('signature') != $signature){
                return response()->json(['status'=>'NOTOK', 'message'=>'Hash mismatch'],500);
            }
        }catch(\Exception $e){
            return response()->json(['status'=>'NOTOK', 'message'=>'Hash mismatch'],500);
        }
        $dr = DocumentRequest::with(['relocation.employee'])->findOrFail($id);
        
        $files = $request->file('files');

        foreach($files as $file){
            $oFile = new File();
            $oFile->path = $file->store('files','s3');
            $oFile->extension = $file->extension();
            $oFile->name = $file->getClientOriginalName();
            $oFile->size = $file->getSize();
            $oFile->mime = $file->getMimeType();
            $oFile->description = "DR ".$dr->description;
            $oFile->visibility = "public";
            $oFile->source = "Customer";
            $oFile->status = 0;
            $oFile->save();
            $dr->relocation->files()->save($oFile);
        }

        $dr->completed_at = now();
        $dr->save();
    }

    public function show(Request $request, $id){
        $request->validate(['signature'=>'required']);
        $signature = hash_hmac('sha256', $id, config('app.key'));

        try{
            if($request->input('signature') != $signature){
                return response()->json(['status'=>'NOTOK', 'message'=>'Hash mismatch'],500);
            }
        }catch(\Exception $e){
            return response()->json(['status'=>'NOTOK', 'message'=>'Hash mismatch'],500);
        }
        $dr = DocumentRequest::with(['relocation.employee'])->findOrFail($id);
        return $dr;
    }

    public function generate(Request $request, $id){
        $request->validate([
            'new_request.email' => 'required|email',
            'new_request.description' => 'required|min:3',

        ]);
       $relocation =  Relocation::findOrFail($id);

       $dr = new DocumentRequest();
       $dr->relocation_id = $relocation->_id;
       $dr->description = $request->input('new_request.description');
       $dr->email = $request->input('new_request.email');
       $dr->employee_id = $relocation->employee_id;
       $dr->save();
        
       if(!empty($request->input('new_request.topic_id'))){
        $topic = ReloMessageTopic::find($request->input('new_request.topic_id'));
        if(!empty($topic)){
            $message = new ReloMessage();
            $message->relo_message_topic_id = $topic->_id;
            $message->user_id = auth()->id();
            $message->content = "Requeste for a document: ".$dr->description;
            $message->relocation_id = $topic->relocation_id;
            $message->save();
            $topic->waiting_employee = true;
            $topic->save();
        }else{
            $dr->delete();
            abort(422, "Invalid topic");
        }
       }

       if(!empty($request->input('new_request.email'))){
           Mail::to([trim($request->input('new_request.email'))])->send(new NewDocRequiredMail($dr));
       }


    }

    public function getFillUrl(Request $request, $id){
        $dr = DocumentRequest::findOrFail($id);

        return response()->json(['url'=>$dr->generateUrl()]);
    }
}
