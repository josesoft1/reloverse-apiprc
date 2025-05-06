<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\File;
use App\Models\MessageTemplate;
use App\Models\Relocation;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{

    public function destroy(Request $request, $id)
    {
        $file = File::findOrFail($id);
        if(!empty($file->path)){
            Storage::disk('s3')->delete($file->path);
        }
        $file->delete();
        return response()->json(['status' => "OK"], 204);
    }

    public function download(Request $request, $id)
    {
        $file = File::findOrFail($id);
        $data = ['url'=>Storage::disk('s3')->temporaryUrl($file->path, now()->addMinutes(30))];
        return response()->json((object)$data);
    }

    public function downloadByPath(Request $request){
        $file = File::where('path',$request->path)->firstOrFail();
        $data = ['url'=>Storage::disk('s3')->temporaryUrl($file->path, now()->addMinutes(30))];
        return response()->json((object)$data);
    }

    public function uploadNote(Request $request){
        $request->validate([
            'description'=>'required',
            'item_id'=>'required',
            'item_type'=>'required'
        ]);

        $element = null;
        switch($request->item_type){
            case 'employee':
                $element = Employee::findOrFail($request->item_id);                
                break;
            case 'relocation':
                $element = Relocation::findOrFail($request->item_id);                
                break;
            case 'message_template':
                $element = MessageTemplate::findOrFail($request->item_id);
                break;
        }
        if($request->has('_id')){
            $file = File::findOrFail($request->input('_id'));
        }else{
            $file = new File();
            $file->source = "BO";
            $file->status = 1;
        }
        
        $file->description = $request->description;
        $file->visibility = $request->visibility;

        if($request->has('file')){
            $base = hash_hmac('sha256', $request->item_id, config('app.key'));      
            $file->path = $request->file->store('files/'. $request->item_type .'/'. $base, 's3');
            $file->extension = $request->file->extension();
            $file->name = $request->file->getClientOriginalName();
            $file->size = $request->file->getSize();
            $file->mime = $request->file->getMimeType();
        }
        $file->save();
        
        $element->files()->save($file);

        return response()->json(['status'=>'OK', 'files'=>$element->files], 200);
    }

    public function validateFile(Request $request, $id){
        $file = File::findOrFail($id);
        $file->status = 1;
        $file->save();
        return $file;
    }
}
