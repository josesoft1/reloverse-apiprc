<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class CompanyController extends Controller
{
    public function index(Request $request){
        $company = Company::query();
        if(!empty($request->input('search'))){
            $company->where('name', 'LIKE', '%'.$request->input('search').'%');
        }
        return $company->orderBy('name','asc')->paginate($request->input('per_page', 25));
    }

    public function show(Request $request, $id){
        $company = Company::with(['employees','rmc'])->findOrFail($id);
        return $company;
    }

    public function store(Request $request){
        $request->validate([
            'customer.name' => 'required',
            'customer.vat' => 'required',
            'customer.address1' => 'required',
            'customer.zip' => 'required'
        ]);


        $company = new Company();
        $company->name = $request->input('customer.name');
        $company->vat = $request->input('customer.vat');
        $company->nin = $request->input('customer.nin');
        $company->address1 = $request->input('customer.address1');
        $company->address2 = $request->input('customer.address2');
        $company->zip = $request->input('customer.zip');
        $company->city = $request->input('customer.city');
        $company->province = $request->input('customer.province');
        $company->country = $request->input('customer.country');
        $company->rmc_id = $request->input('customer.rmc._id');
        $company->status = 1;
        $company->save();
        return $company;
    }

    
    public function update(Request $request, $id){
        $request->validate([
            'customer.name' => 'required',
            'customer.vat' => 'required',
            'customer.address1' => 'required',
            'customer.zip' => 'required'
        ]);


        $company = Company::findOrFail($id);
        $company->name = $request->input('customer.name');
        $company->vat = $request->input('customer.vat');
        $company->nin = $request->input('customer.nin');
        $company->address1 = $request->input('customer.address1');
        $company->address2 = $request->input('customer.address2');
        $company->zip = $request->input('customer.zip');
        $company->city = $request->input('customer.city');
        $company->province = $request->input('customer.province');
        $company->country = $request->input('customer.country');
        $company->rmc_id = $request->input('customer.rmc._id');
        $company->status = 1;
        $company->save();
        return $company;
    }

    public function disable(Request $request, $id){
        $company = Company::findOrFail($id);
        $company->status = 0;
        $company->save();
        
        return $company;
    }
    public function enable(Request $request, $id){
        $company = Company::findOrFail($id);
        $company->status = 1;
        $company->save();

        return $company;
    }
}
