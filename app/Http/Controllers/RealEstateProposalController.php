<?php

namespace App\Http\Controllers;

use App\Mail\NewRealEstateProposal;
use App\Models\File;
use App\Models\RealEstateProperty;
use App\Models\RealEstateProposal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class RealEstateProposalController extends Controller
{
    public function store(Request $request){
        $request->validate([
            'identification' => 'required'
        ]);
        $property = RealEstateProperty::findOrFail($request->input('identification'));

        $rep = new RealEstateProposal();
        $rep->real_estate_property_id = $property->_id;
        $rep->relocation_id = $request->input('relocation_id');
        $rep->consultant_id = auth()->user()->_id;
        $rep->save();
        
        $rep->load(['relocation']);
        
        /**$emailTo = [$rep->relocation->employee->email];
        Mail::to($emailTo)->send(new NewRealEstateProposal($rep));**/
    }

    public function delete(Request $request, RealEstateProposal $rep){      
        if(!empty($rep->attachment_id)){
            $file = File::findOrFail($rep->attachment_id);
            Storage::disk('s3')->delete($file->path);
            $file->delete();
        }

        $rep->delete();
    }

    public function select(Request $request, RealEstateProposal $rep){

        $rep_w = RealEstateProposal::where('relocation_id', $rep->relocation_id)->get();
        foreach($rep_w as $rep_r){
            if($rep_r->_id == $rep->_id){
                $rep_r->selected = 1;
                $rep_r->save();
            }else {
                $rep_r->selected = 0;
                $rep_r->save();
            }
        }
        
    }

    public function send(Request $request, RealEstateProposal $rep){      
        $request->validate(['email'=>'required|array','email.*' => 'required|email']);
        Mail::to($request->input('email'))->send(new NewRealEstateProposal($rep));
    }

    public function updateRating(Request $request, RealEstateProposal $rep){      
        $rep->rating_feedback = $request->input('new_rating');
        $rep->save();
    }
}
