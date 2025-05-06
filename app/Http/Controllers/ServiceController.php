<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index(Request $request){
        $services = Service::query();
        /*
        if(!empty($request->input('search'))){
            $services->where('name','like',"%{$request->input('search','')}%");
        }
        return $services->orderBy('name','asc')->paginate($request->input('per_page', 25));
        */
        if (!empty($request->input('search'))) {
            $services->where('name', 'LIKE', '%' . $request->input('search') . '%');
        }
        return $services->orderBy('name', 'asc')->paginate($request->input('per_page', 20));
    }

    public function store(Request $request){
        $request->validate([
            'service.name' => 'required',
            'service.price' => 'required|min:0'
        ]);

        $service = new Service();
        $service->name =        $request->input('service.name');
        $service->price =       $request->input('service.price');
        $service->description =    $request->input('service.description');
        $service->date =    $request->input('service.date');
        $service->reference =    $request->input('service.reference');
        $service->plannable =    (bool)$request->input('service.plannable');
        $service->status = 1;
        $service->save();

        return $service;
    }

    public function update(Request $request, $id){
        $service = Service::findOrFail($id);
        $service->fill($request->input('service'));
        $service->save();
    }
    public function show(Request $request, $id){
        return Service::findOrFail($id);
    }
}
