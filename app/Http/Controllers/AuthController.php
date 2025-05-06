<?php

namespace App\Http\Controllers;

use App\Mail\SendRecoveryLink;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Employee;
use App\Models\Rmc;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
        /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login','refresh','send_recovery_link','verifyResetData', 'recovery']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $request->validate(['frontend' => 'required:in:admin,employee,rmc']);
        
        $credentials = request(['email', 'password']);

        if($request->input('frontend') == 'employee'){
            $employee = Employee::where('email', $request->input('email'))->first();
            if(empty($employee)){
                return response()->json(['error' => 'Unauthorized. Employee not found'], 401);
            }

            $emp_user = User::where('employee_id', $employee->_id)->where('role',User::ROLE_EMPLOYEE)->first();
            if(empty($emp_user)){
                return response()->json(['error' => 'Unauthorized. No matching user'], 401);
            }

            if(Hash::check($request->input('password'), $emp_user->password)){
                $token = auth('api')->login($emp_user);
                return $this->respondWithToken($token);
            }else{
                return response()->json(['error' => 'Unauthorized. Invalid credentials'], 401);
            }
        }
        
        if($request->input('frontend') == 'admin'){
            $user = User::where('email', $request->input('email'))->where('role',User::ROLE_ADMIN)->first();
            if(empty($user)){
                return response()->json(['error' => 'Unauthorized. User not found'], 401);
            }
            
            if(Hash::check($request->input('password'), $user->password)){
                $token = auth('api')->login($user);
                return $this->respondWithToken($token);
            }else{
                return response()->json(['error' => 'Unauthorized. Invalid credentials'], 401);
            }
        }

        if($request->input('frontend') == 'rmc'){
            $rmc = Rmc::where('email', $request->input('email'))->first();
            if(empty($rmc)){
                return response()->json(['error' => 'Unauthorized. Rmc not found'], 401);
            }

            $rmc_user = User::where('rmc_id', $rmc->_id)->where('role',User::ROLE_RMC)->first();
            
            if(empty($rmc_user)){
                return response()->json(['error' => 'Unauthorized. No matching user'], 401);
            }

            if(Hash::check($request->input('password'), $rmc_user->password)){
                $token = auth('api')->login($rmc_user);
                return $this->respondWithToken($token);
            }else{
                return response()->json(['error' => 'Unauthorized. Invalid credentials'], 401);
            }
        }
        
        return response()->json(['error' => 'Unauthorized. Cannot attempt.'], 401);
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json(auth('api')->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth('api')->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth('api')->refresh());
    }

    public function send_recovery_link(Request $request){
        $request->validate(['email'=>'required|email']);

        $user = User::where('email',$request->email)->where('role',User::ROLE_ADMIN)->firstOrFail();

        $recovery_object = new \stdClass();
        $recovery_object->old = $user->password;
        $recovery_object->due = now()->addHours(2);
        $recovery_object->email = $user->email;
        $recovery_object_encoded = Crypt::encryptString(json_encode($recovery_object));

        Mail::to($user->email)->send(new SendRecoveryLink($user));
    }

    public function verifyResetData(Request $request){
        $request->validate([
            'data' => 'required'
        ]);
        try{
            $obj = json_decode(Crypt::decryptString($request->data));
            if(\Carbon\Carbon::parse($obj->due)->gte(now())){
                $user = User::select(['name','id','email'])->where('email', $obj->email)->where('password',$obj->old)->first();
                if(empty($user)){
                    return response()->json(['STATUS'=>'NOTOK','message'=>'Already used'],403);
                }
                return response()->json(['STATUS'=>'OK','USER'=>$user]);
            }else{
                return response()->json(['STATUS'=>'NOTOK','message'=>'This link is overdue. Please try it again with a new request.'],403);
            }
        }catch(\Exception $e){
            return response()->json(['STATUS'=>'NOTOK','message'=>'Error'],403);
        }
    }

    public function recovery(Request $request){
        $request->validate([
            'data' => 'required',
            'new_password' => 'required'
        ]);
        $obj = json_decode(Crypt::decryptString($request->data));
        
        if(\Carbon\Carbon::parse($obj->due)->gte(now())){
            $user = User::select(['name','id','email'])->where('email', $obj->email)->where('password',$obj->old)->first();
            if(empty($user)){
                return response()->json(['STATUS'=>'NOTOK','message'=>'Token has already been used'],403);
            }
            $user->password = Hash::make($request->new_password);
            $user->save();
            return response()->json(['STATUS'=>'OK']);
        }else{
            return response()->json(['STATUS'=>'NOTOK','message'=>'Token has expired'],403);
        }
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60
        ]);
    }
}
