<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Paf;

class PafController extends Controller
{
    //
    public function index(Request $request)
    {
        $pafs = Paf::query();
        /*
        if(!empty($request->input('search'))){
            $pafs->where(function($q) use($request){
                $q->orWhere("_id","LIKE","%{$request->input('search','')}%");
                $q->orWhere('real_estate_proposal_id','LIKE','%'.$request->input('search').'%');
                $q->orWhere('tenant_name','LIKE','%'.$request->input('search').'%');
                $q->orWhere('tenant_lastname','LIKE','%'.$request->input('search').'%');
                $q->orWhere('tenant_address','LIKE','%'.$request->input('search').'%');
            });
        }
        */
        if (!empty($request->input('search'))) {
            $pafs->where('_id', 'LIKE', '%' . $request->input('search') . '%');
            $pafs->orWhere('real_estate_proposal_id', 'LIKE', '%' . $request->input('search') . '%');
            $pafs->orWhere('tenant_name', 'LIKE', '%' . $request->input('search') . '%');
            $pafs->orWhere('tenant_lastname', 'LIKE', '%' . $request->input('search') . '%');
            $pafs->orWhere('tenant_address', 'LIKE', '%' . $request->input('search') . '%');
        }
        
        // return $pafs->orderBy('contact_name', 'asc')->paginate($request->input('per_page', 25));
        return $pafs->paginate($request->input('per_page','20'));
    }

    public function store(Request $request)
    {
        /*
        $paf = new Paf();
        $paf->tenant_name = $request->input('tenant_name');
        $paf->tenant_lastname = $request->input('tenant_lastname');
        $paf->tenant_address = $request->input('tenant_address');
        $paf->save();
        return $paf;
        */
        $paf = Paf::create($request->all());
        return response()->json($paf, 201);
    }

    /*
    public function update(Request $request, $id)
    {
        $paf = Paf::findOrFail($id);
        $paf->fill($request->input('paf'));
        $paf->save();
    }
    */
    public function update(Request $request, $id)
    {
        $paf = Paf::findOrFail($id);
        $paf->update($request->input('paf', []));
        return response()->json($paf);
    }

    public function show(Request $request, $id){
        return Paf::findOrFail($id);
    }

    /*
    public function show2(Request $request, $id){
        return Paf::where('relocation_id', $id)->firstOrFail();
    }
    */
    public function show2(Request $request, $id)
    {
        // Se non ci sono campi extra:
        $paf = Paf::firstOrCreate(['relocation_id' => $id]);
        return response()->json($paf);
    }

}
