<?php

namespace App\Http\Controllers;

use App\Models\MessageTemplate;
use Illuminate\Http\Request;

class MessageTemplateController extends Controller
{
    public function index(Request $request){
        $templates = MessageTemplate::query();
        if(!empty($request->search)){
            $templates->where(function($q) use ($request){
                $q->where('name', 'LIKE', '%'.$request->input('search').'%');
            });
        }

        if($request->input('with_attachments',false) == 'true'){
            $templates->with(['files' => function($q){$q->whereNotNull('path');}]);
        }

        return $templates->paginate($request->input('per_page',15));
    }

    public function store(Request $request){
        $request->validate(['name'=>'required|min:3']);

        $template = new MessageTemplate();
        $template->name = $request->input('name');
        $template->template = $request->input('template');
        $template->status = 0;
        $template->user_id = auth()->id();
        $template->save();

        return $template; //jose
    }
    public function update(Request $request, MessageTemplate $template){
        $request->validate(['name'=>'required|min:3']);

        $template->name = $request->input('name');
        $template->template = $request->input('template');
        $template->status = 0;
        $template->dr_requirements = $request->input('dr_requirements');
        $template->save();
    }

    public function show(Request $request, MessageTemplate $template){
        $template->load(['files']);
        return $template;
    }
}
