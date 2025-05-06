<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Rmc;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Mail\RmcNewPasswordGenerated;
use App\Models\User;

class RmcController extends Controller
{
    public function index(Request $request)
    {
        $rmcs = Rmc::query();
        if (!empty($request->input('search'))) {
            $rmcs->where('name', 'LIKE', '%' . $request->input('search') . '%');
            $rmcs->orWhere('contact_name', 'LIKE', '%' . $request->input('search') . '%');
        }
        return $rmcs->orderBy('name', 'asc')->paginate($request->input('per_page', 20));
    }

    public function store(Request $request)
    {
        $request->validate([
            'rmc.name' => 'required',
            'rmc.vat' => 'required',
            'rmc.address1' => 'required',
            'rmc.email' => 'required|email|unique:rmcs,email'
        ]);


        $rmc = new Rmc();
        $rmc->name = $request->input('rmc.name');
        $rmc->vat = $request->input('rmc.vat');
        $rmc->nin = $request->input('rmc.nin');
        $rmc->address1 = $request->input('rmc.address1');
        $rmc->address2 = $request->input('rmc.address2');
        $rmc->zip = $request->input('rmc.zip');
        $rmc->city = $request->input('rmc.city');
        $rmc->province = $request->input('rmc.province');
        $rmc->country = $request->input('rmc.country');
        $rmc->status = 1;
        $rmc->contact_name = $request->input('rmc.contact_name');
        $rmc->email = $request->input('rmc.email');
        $rmc->phone = $request->input('rmc.phone');
        $rmc->contact_note = $request->input('rmc.contact_note');
        $rmc->contact_name_2 = $request->input('rmc.contact_name_2');
        $rmc->email_2 = $request->input('rmc.email_2');
        $rmc->phone_2 = $request->input('rmc.phone_2');
        $rmc->contact_note_2 = $request->input('rmc.contact_note_2');

        $rmc->save();
        return $rmc;
    }

    public function update(Request $request, Rmc $rmc)
    {
        $request->validate([
            'rmc.name' => 'required',
            'rmc.vat' => 'required',
            'rmc.address1' => 'required'
        ]);

        $rmc->name = $request->input('rmc.name');
        $rmc->vat = $request->input('rmc.vat');
        $rmc->nin = $request->input('rmc.nin');
        $rmc->address1 = $request->input('rmc.address1');
        $rmc->address2 = $request->input('rmc.address2');
        $rmc->zip = $request->input('rmc.zip');
        $rmc->city = $request->input('rmc.city');
        $rmc->province = $request->input('rmc.province');
        $rmc->country = $request->input('rmc.country');
        $rmc->status = 1;
        $rmc->contact_name = $request->input('rmc.contact_name');
        $rmc->email = $request->input('rmc.email');
        $rmc->phone = $request->input('rmc.phone');
        $rmc->contact_note = $request->input('rmc.contact_note');
        $rmc->contact_name_2 = $request->input('rmc.contact_name_2');
        $rmc->email_2 = $request->input('rmc.email_2');
        $rmc->phone_2 = $request->input('rmc.phone_2');
        $rmc->contact_note_2 = $request->input('rmc.contact_note_2');

        $rmc->save();
        return $rmc;
    }

    public function show(Request $request, $id)
    {
        return Rmc::with(['holdings'])->findOrFail($id);
    }

    public function enable(Request $request, $id)
    {
        $rmc = Rmc::findOrFail($id);
        $rmc->status = 1;
        $rmc->save();
        return $rmc;
    }

    public function disable(Request $request, $id)
    {
        $rmc = Rmc::findOrFail($id);
        $rmc->status = 0;
        $rmc->save();
        return $rmc;
    }

    public function sendNewCredential(Request $request, Rmc $rmc)
    {

        $user = User::where('rmc_id',$rmc->_id)->first();
        if(empty($user)){
            $user = new User();
            $user->rmc_id = $rmc->_id;
            $user->name = $rmc->name;
            $user->surname = null;
            $user->email = $rmc->_id."@rmc.mediacrm.it";
            $user->role = User::ROLE_RMC;
            $user->save();
        }

        $password = Str::random(10);
        $user->password = Hash::make($password);
        $user->save();

        Mail::to($rmc->email)->send(new RmcNewPasswordGenerated($rmc, $password));
    }
}
