<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request){
        $request->validate(
            [
                'type' => 'required|in:bo,consultant,employee',
            ]
        );

        $users = User::query();

        if(!empty($request->search)){
            $users->where(function($q) use ($request){
                $q->where('name', 'LIKE', '%'.$request->input('search').'%');
                $q->orWhere('surname', 'LIKE', '%'.$request->input('search').'%');
                $q->orWhere('phone', 'LIKE', '%'.$request->input('search').'%');
                $q->orWhere('email', 'LIKE', '%'.$request->input('search').'%');

            });
        }

        switch($request->input('type')){
            case 'bo':
                $users->where('role', 0);
                break;
        }

        return $users->paginate($request->input('per_page',15));
    }
}
