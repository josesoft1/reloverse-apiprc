<?php

namespace App\Http\Controllers;

use App\Mail\EmployeeNewPasswordGenerated;
use App\Models\Employee;
use App\Models\Company;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class EmployeeController extends Controller
{
    public function index(Request $request){
        $employees = Employee::query();


        if(!empty($request->input('search'))){
            $employees->where(function($q) use($request){
                $q->where('name','LIKE','%'.$request->input('search').'%');
                $q->orWhere('surname','LIKE','%'.$request->input('search').'%');
                $q->orWhere('email','LIKE','%'.$request->input('search').'%');
            });
        }

        return $employees->paginate($request->input('per_page','25'));
    }

    public function show(Request $request, $id){
        $e = Employee::with(['company','files'])->findOrFail($id);
        return $e;
    }

    public function update(Request $request, $id){
        $e = Employee::with(['company','files'])->findOrFail($id);
        $e->title = $request->input('e.title');
        $e->name = $request->input('e.name');
        $e->surname = $request->input('e.surname');
        $e->date_of_birth = Carbon::parse($request->input('e.date_of_birth'));
        $e->nationality = $request->input('e.nationality');
        $e->sex = $request->input('e.sex');
        $e->origin_country = $request->input('e.origin_country');
        $e->address1 = $request->input('e.address1');
        $e->address2 = $request->input('e.address2');
        $e->city = $request->input('e.city');
        $e->province = $request->input('e.province');
        $e->zip = $request->input('e.zip');
        $e->vip = $request->input('e.vip',false);
        $e->email = $request->input('e.email');
        $e->phone = $request->input('e.phone');
        $e->mobile = $request->input('e.mobile');
        $e->work_email = $request->input('e.work_email');
        $e->work_phone = $request->input('e.work_phone');
        $e->work_mobile = $request->input('e.work_mobile');
        $e->cc = $request->input('e.cc');
        $e->save();

        if(User::where('employee_id', $e->_id)->count() == 0){
            $u = new User();
            $u->name = $e->name;
            $u->surname = $e->surname;
            $u->email = $e->_id."@mediacrm.it";
            $u->password = Hash::make($e->_id.$e->full_name);
            $u->role = User::ROLE_EMPLOYEE;
            $u->employee_id = $e->_id;
            $u->save();
        }else{
            $u = User::where('employee_id', $e->_id)->first();
            $u->name = $e->name;
            $u->surname = $e->surname;
            $u->email = $e->_id."@mediacrm.it";
            $u->password = Hash::make("ltsa6127");
            $u->role = User::ROLE_EMPLOYEE;
            $u->save();
        }
    }

    public function store(Request $request){
        $request->validate([
            'e.name' => 'required|min:3',
            'e.surname' => 'required|min:3',
        ]);
        $company = Company::findOrFail($request->input('e.company._id'));
        $e = new Employee();
        $e->title = $request->input('e.title');
        $e->name = $request->input('e.name');
        $e->surname = $request->input('e.surname');
        $e->date_of_birth = Carbon::parse($request->input('e.date_of_birth'));
        $e->nationality = $request->input('e.nationality');
        $e->sex = $request->input('e.sex');
        $e->origin_country = $request->input('e.origin_country');
        $e->address1 = $request->input('e.address1');
        $e->address2 = $request->input('e.address2');
        $e->city = $request->input('e.city');
        $e->province = $request->input('e.province');
        $e->zip = $request->input('e.zip');
        $e->vip = $request->input('e.vip',false);
        $e->email = $request->input('e.email');
        $e->phone = $request->input('e.phone');
        $e->mobile = $request->input('e.mobile');
        $e->company_id = $company->_id;
        $e->cc = $request->input('e.cc');
        $e->save();

        $u = new User();
        $u->name = $e->name;
        $u->surname = $e->surname;
        $u->email = $e->_id."@mediacrm.it";
        $u->password = Hash::make($e->_id.$e->full_name);
        $u->role = User::ROLE_EMPLOYEE;
        $u->employee_id = $e->_id;
        $u->save();
    }

    public function sendNewCredential(Request $request, Employee $employee){
        $new_password = Str::random(8);
        $u = User::where('employee_id', $employee->_id)->first();
        $u->name = $employee->name;
        $u->surname = $employee->surname;
        $u->email = $employee->_id."@mediacrm.it";
        $u->password = Hash::make($new_password);
        $u->role = User::ROLE_EMPLOYEE;
        $u->save();
        Mail::to($employee->email)->send(new EmployeeNewPasswordGenerated($employee,$new_password));
    }


}
