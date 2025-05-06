<?php

namespace App\Http\Controllers;

use App\Mail\NewMessageOnTopic;
use Illuminate\Http\Request;

use App\Models\ReloMessageTopic;
use App\Models\ReloMessage;
use App\Models\Employee;
use App\Models\File;
use App\Models\User;
use App\Models\Relocation;
use App\Models\DocumentRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ReloMessageTopicController extends Controller
{

    public function index(Request $request)
    {
        $topics = ReloMessageTopic::query();

        if ($request->input('only_active', false) == true) {
            $topics->where('status', '!=', 1);
        }

        if ($request->has('relocation_id')) {
            $topics->where('relocation_id', $request->input('relocation_id'));
        }

        return $topics->get();
    }


    public function store(Request $request, Relocation $relocation)
    {
        $request->validate(['topic' => 'required|min:5']);

        $topic = new ReloMessageTopic($request->only(['topic']));
        $topic->relocation_id = $relocation->_id;
        $topic->status = 0;
        $topic->save();

        return response()->json(['status' => 'OK', 'topic' => $topic]);
    }

    public function show(Request $request, ReloMessageTopic $topic)
    {
        $topic->load(
            [
                'messages' => function ($q) {
                    $q->orderBy('created_at', 'desc');
                },
                'messages.user' => function ($q) {
                    $q->select(['_id', 'name', 'surname']);
                },
                'messages.employee' => function ($q) {
                    $q->select(['_id', 'name', 'surname']);
                }, 'relocation.employee'
            ]
        );
        $topic->waiting = false;
        return $topic;
    }

    public function newMessage(Request $request, ReloMessageTopic $topic)
    {

        $request->validate([
            'content' => 'required',
            'to' => 'required|array',
            'to.*' => 'email',
            'files' => 'array',
            'files.*' => 'file'
        ]);


        $content = $request->input('content');
        if (auth()->user()->role == User::ROLE_ADMIN) {
            $content = str_replace('$$name$$', $topic->relocation->employee->name, $content);
            $content = str_replace('$$surname$$', $topic->relocation->employee->surname, $content);
            $content = str_replace('$$full_name$$', $topic->relocation->employee->full_name, $content);
            $content = str_replace('$$mobile$$', $topic->relocation->employee->mobile, $content);
            $content = str_replace('$$email$$', $topic->relocation->employee->email, $content);
            $content = str_replace('$$company$$', $topic->relocation->employee->company->name, $content);
            $content = str_replace('$$my_full_name$$', auth()->user()->full_name, $content);
            $content = str_replace('$$my_name$$', auth()->user()->name, $content);
            $content = str_replace('$$my_surname$$', auth()->user()->name, $content);
            $content = str_replace('$$my_email$$', auth()->user()->email, $content);
        }


        $message = new ReloMessage();
        $message->content = $content;
        $message->relocation_id = $topic->relocation->_id;
        $message->relo_message_topic_id = $topic->_id;

        if (auth()->user()->role == User::ROLE_ADMIN) {
            $message->user_id = auth()->id();
        }

        if (auth()->user()->role == User::ROLE_EMPLOYEE) {
            $message->employee_id = $topic->relocation->employee->_id;
        }

        $message->save();
        try{
            if (auth()->user()->role == User::ROLE_ADMIN) {
                $message->topic->waiting_employee = true;
                $message->topic->waiting = false;
                $message->topic->save();
    
    
                $email = Mail::to($request->input('to'));


                $template_files = [];
                if (is_array($request->input('template_files', [])) || is_object($request->input('template_files', [])))
                {
                    $template_files = [];
                    foreach ($request->input('template_files', []) as $template) {
                        $return_data = $template;
                        if (gettype($template) == 'string') {
                            $return_data = json_decode($template);
                        }
                        $template_files[] = $return_data;
                    }
                }

                
                $uploaded_file = [];
                if (is_array($request->file('files')) || is_object($request->file('files')))
                {
                    $uploaded_file = [];
                    foreach($request->file('files') as $upload_file){
                    
                        $file = new File();
        
                        $file->source = "BO";
                        $file->status = 1;
            
                        $file->description = $request->description;
                        $file->visibility = 'public';
            
                        $base = hash_hmac('sha256', $message->topic->_id, config('app.key'));
                        $file->path = $upload_file->store('files/' . $request->item_type . '/' . $base, 's3');
                        $file->extension = $upload_file->extension();
                        $file->name = $upload_file->getClientOriginalName();
                        $file->size = $upload_file->getSize();
                        $file->mime = $upload_file->getMimeType();
        
                        $file->save();
                        $uploaded_file[] = $file;
                    }
                }
    

                $email->send(new NewMessageOnTopic($message, array_merge($template_files, $uploaded_file)));
            }
        }catch(\Exception $e){
            $message->delete();
            throw $e;
        }


        if (auth()->user()->role == User::ROLE_EMPLOYEE) {
            $message->topic->waiting_employee = false;
            $message->topic->waiting = true;
            $message->topic->save();
        }

        if (!empty($request->input('template'))) {
            $dr_reqs = $request->input('template.dr_requirements', []);
            if (gettype($dr_reqs) == 'string') {
                $dr_reqs = json_decode($dr_reqs);
            }
            foreach ($dr_reqs as $dr_req) {
                if ($dr_req['value'] == true) {
                    $file_req = File::find($dr_req['key']);
                    if (!empty($file_req)) {
                        $dr = new DocumentRequest();
                        $dr->relocation_id = $message->topic->relocation_id;
                        $dr->description = $dr_req['description'];
                        $dr->email = $message->topic->relocation->employee->email;
                        $dr->employee_id = $message->topic->relocation->employee_id;
                        $dr->save();
                    }
                }
            }
        }

        return response()->json(['status' => 'OK']);

        /*
            $message->topic->save();
            Mail::to("tiziano93@gmail.com")->send(new NewMessageOnTopic($message));
            */

        //Mail::to($message->to)->send(new NewReloMessage($message));
    }

    public function close(Request $request, ReloMessageTopic $topic)
    {
        $topic->status = 1;
        $topic->closed_at = now();
        $topic->save();
        return $topic;
    }

    public function search(Request $request, Relocation $relocation)
    {
        $messages_query = ReloMessage::where('relocation_id', $relocation->_id)->with(['topic']);
        $messages_query->where('content', 'like', '%' . $request->input('search') . '%')->get();
        $results =  $messages_query->get()->map(function ($e) use ($request) {
            $e->content = strip_tags($e->content);
            $e->content = str_ireplace($request->input('search'), '<span style="background-color: #FFFF00">' . $request->input('search') . '</span>', $e->content);
            $e->content = '<span>' . Str::excerpt($e->content, $request->input('search'), [
                'radius' => strlen($request->input('search')) + 50,
            ]) . '</span>';
            return $e;
        });
        return $results;
    }

    public function download(Request $request, ReloMessageTopic $topic)
    {
        $topic->load(['messages', 'messages.user', 'messages.employee']);
        $filename = 'topic_' . $topic->_id . '_' . date('Y-m-d_H-i-s') . '.txt';
        $file_content = "";
        foreach ($topic->messages as $message) {
            $content = $message->content;
            $content = str_replace('<br>', PHP_EOL, $content);
            $content = str_replace('<br/>', PHP_EOL, $content);
            $content = strip_tags($content);
            $content = str_replace('&nbsp;', ' ', $content);
            $content = str_replace('&amp;', '&', $content);
            $content = str_replace('&quot;', '"', $content);
            $content = str_replace('&lt;', '<', $content);
            $content = str_replace('&gt;', '>', $content);
            $content = str_replace('&apos;', "'", $content);
            $content = str_replace('&cent;', '¢', $content);
            $content = str_replace('&pound;', '£', $content);
            $content = str_replace('&yen;', '¥', $content);
            $content = str_replace('&euro;', '€', $content);
            $content = str_replace('&sect;', '§', $content);
            $content = str_replace('&copy;', '©', $content);
            $content = str_replace('&reg;', '®', $content);
            $content = str_replace('&trade;', '™', $content);
            $content = str_replace('&times;', '×', $content);
            $file_content .= '----------------------------------------' . PHP_EOL;
            $file_content .= $message->created_at->format('d/m/Y H:i:s') . ' - ' . (!empty($message->user) ? $message->user->full_name : $message->employee->full_name) . ': ' . $content . PHP_EOL;
        }
        return response($file_content)->header('Content-Type', 'text/plain')->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }
}
