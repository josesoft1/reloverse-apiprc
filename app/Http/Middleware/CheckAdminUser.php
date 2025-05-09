<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;

class CheckAdminUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if(!auth('api')->guest() && auth('api')->user()->role == User::ROLE_ADMIN){
            return $next($request);
        }else{
            return response()->json(['status'=>'NOTOK','message'=>'User is not authorized to perform this route. Logged'],401);
        }
    }
}
