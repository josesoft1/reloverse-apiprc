<?php

namespace App\Http\Controllers;

use App\Models\Consultant;
use Illuminate\Http\Request;

class ConsultantController extends Controller
{
    public function index(Request $request){
        $consultants = Consultant::query();

        if(!empty($request->search)){
            $consultants->where(function($q) use ($request){
                $q->where('name', 'LIKE', '%'.$request->input('search').'%');
                $q->orWhere('surname', 'LIKE', '%'.$request->input('search').'%');
                $q->orWhere('phone', 'LIKE', '%'.$request->input('search').'%');
                $q->orWhere('email', 'LIKE', '%'.$request->input('search').'%');

            });
        }

        if($request->has('active')){
            $consultants->where('active', $request->active == 'true', false);
        }

        return $consultants->paginate($request->input('per_page',15));
    }

    public function show(Request $request, Consultant $consultant){
        return $consultant;
    }
    
    public function update(Request $request, Consultant $consultant){
        $request->validate([
            'name' => 'required|min:3',
            'surname' => 'required|min:3',
            'email' => 'required|email',
        ]);

        if(!checkEmail($request->input('email'))){
            throw \Illuminate\Validation\ValidationException::withMessages(['email' => 'The email address appears to be non-existent']);
        }
    
        $consultant->name = trim($request->input('name'));
        $consultant->surname = trim($request->input('surname'));
        $consultant->email = trim($request->input('email'));
        $consultant->mobile = trim($request->input('mobile'));
        $consultant->save();
        return $consultant;
    }

    public function store(Request $request){
        $request->validate([
            'name' => 'required|min:3',
            'surname' => 'required|min:3',
            'email' => 'required|email',
        ]);

        if(!checkEmail($request->input('email'))){
            throw \Illuminate\Validation\ValidationException::withMessages(['email' => 'The email address appears to be non-existent']);
        }

        $consultant = new Consultant();

        $consultant->name = trim($request->input('name'));
        $consultant->surname = trim($request->input('surname'));
        $consultant->email = trim($request->input('email'));
        $consultant->mobile = trim($request->input('mobile'));
        $consultant->active = true;
        $consultant->save();
    }
}
