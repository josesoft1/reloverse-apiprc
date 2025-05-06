<?php
/**
 * @author Tiziano Zorzo <info@consulenza.app>
 * @copyright 2022 CST
 */

namespace App\Http\Controllers;

use App\Jobs\GenerateREPhotoThumbnails;
use Illuminate\Http\Request;
use App\Models\RealEstateProperty;
use App\Models\RealEstatePropertyPhoto;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Response;

class RealEstatePropertyController extends Controller
{

    public function index(Request $request){
        $reps = RealEstateProperty::with('photos')->whereIn('typology', $request->input('typology', []));
        $paginator = $reps->paginate(30);
        $paginator->getCollection()->transform(function ($value) {
            $value->thumbnail_images = $value->photos->map(function($e, $i){
                //return Storage::disk('s3')->url($e->path);
                if(empty($e->thumb_300_path)){
                    return null;
                }
                if($i == 0){
                    return Storage::disk('s3')->url($e->thumb_300_path);
                }else{
                    return Storage::disk('s3')->url($e->thumb_80_path);
                }
            });
            $value->images = $value->photos->map(function($e){
                return Storage::disk('s3')->url($e->path);
            });
            return $value;
        });
        return $paginator;
    }

    /**
     * Store a new realestateproperty
     *
     * @param Request $request
     * @return Response 
     * @return void
     */
    public function store(Request $request){
        $request->validate([
            'title'=>'required|min:3',
            'negotation' => 'required|in:Rental,Sale',
            'geocoded_address' => 'required',
            'geocoded_address.place_id' => 'required',
            'price' => 'required|numeric',
            'price' => 'required|numeric',
            'other_price' => 'numeric',
            'typology' => 'required|in:apartment,villa,row-home,semi-detached-home,detached-home',
            'n_bath' => 'required|numeric',
            'n_bed' => 'required|numeric',
            'n_balcony' => 'required|numeric',
            'n_garage_slots' => 'required|numeric',
            'square_meters' => 'required|numeric'
        ]);

        $property = new RealEstateProperty();
        $property->fill($request->all());
        $property->save();

        $res = self::getLatLng($property->geocoded_address['place_id']);
        if(!empty($res)){
            $property->lat = $res['lat'];
            $property->lng = $res['lng'];
            $property->save();
        }


        return $property;

    }


    /**
     * Update the information about a given RealEstateProperty
     *
     * @param Request $request
     * @param RealEstateProperty $property
     * @return Response
     */
    public function update(Request $request, RealEstateProperty $property){
        $request->validate([
            'title'=>'required|min:3',
            'negotation' => 'required|in:Rental,Sale',
            'geocoded_address' => 'required',
            'geocoded_address.place_id' => 'required',
            'price' => 'required|numeric',
            'price' => 'required|numeric',
            'other_price' => 'numeric',
            'typology' => 'required|in:apartment,villa,row-home,semi-detached-home,detached-home',
            'n_bath' => 'required|numeric',
            'n_bed' => 'required|numeric',
            'n_balcony' => 'required|numeric',
            'n_garage_slots' => 'required|numeric',
            'square_meters' => 'required|numeric'
        ]);

        $original_place_id = $property->geocoded_address['place_id'];

        $property->fill($request->all());
        $property->save();

        if(!empty($property->geocoded_address['place_id']) && $original_place_id != $property->geocoded_address['place_id']){
            $res = self::getLatLng($property->geocoded_address['place_id']);
            if(!empty($res)){
                $property->lat = $res['lat'];
                $property->lng = $res['lng'];
                $property->save();
            }
        }

        $property->load(['photos']);
        return $property;
    }

    public function show(Request $request, RealEstateProperty $property){
        if(empty($property->lat) || empty($property->lng) && !empty($property->geocoded_address['place_id'])){       
            $res = self::getLatLng($property->geocoded_address['place_id']);
            if(!empty($res)){
                $property->lat = $res['lat'];
                $property->lng = $res['lng'];
                $property->save();
            }
        }
        $property->load(['photos']);
        $property->photos = $property->photos->map(function($e){
            $e->id = $e->_id;
            $e->url = Storage::disk('s3')->url($e->path);
            return $e;
        });
        return $property;
    }

    public function showPublic(Request $request, RealEstateProperty $property){
        $request->validate(['signature'=>'required']);

        if(hash_hmac('sha256', $property->_id, config('app.key')) != $request->signature){
            //abort(401);
        }

        $property->load(['photos']);

        $property->photos = $property->photos->map(function($e){
            $e->thumbnail_images = $e->photos->map(function($e, $i){
                if(empty($e->thumb_300_path)){
                    return null;
                }
                if($i == 0){
                    return Storage::disk('s3')->url($e->thumb_300_path);
                }else{
                    return Storage::disk('s3')->url($e->thumb_80_path);
                }
            });
            $e->images = $e->photos->map(function($e){
                return Storage::disk('s3')->url($e->path);
            });
        });


        return $property;

    }

    /**
     * Delete a given RealEstateProperty
     *
     * @param Request $request
     * @param RealEstateProperty $property
     * @return Response
     */
    public function delete(Request $request, RealEstateProperty $property){
        $property->delete();
        return true;
    }

    /**
     * Attach a photo the RealEstateProperty
     *
     * @param Request $request
     * @param RealEstateProperty $property
     * @return Response 
     */
    public function attachPhoto(Request $request, RealEstateProperty $property){
        $property_photo = new RealEstatePropertyPhoto();
        $base = hash_hmac('sha256', $property->_id, config('app.key'));  

        $property_photo->path = $request->file->store('re_photos/'. $base, 's3');
        $property_photo->extension = $request->file->extension();
        $property_photo->name = $request->file->getClientOriginalName();
        $property_photo->size = $request->file->getSize();
        $property_photo->mime = $request->file->getMimeType();
        $property_photo->real_estate_property_id = $property->_id;
        $property_photo->save();

        dispatch(new GenerateREPhotoThumbnails($property_photo));
        return $property_photo;
    }

    /**
     * Delete a photo from the RealEstateProperty
     *
     * @param Request $request
     * @param RealEstatePropertyPhoto $photo
     * @return Response
     */
    public function deletePhoto(Request $request, RealEstatePropertyPhoto $photo){
        $photo->delete();
        return true;
    }

    /**
     * Interrogate the gmaps api about a given place_id and extract the latitude/longitude
     *
     * @param String $place_id
     * @return Array
     */
    private static function getLatLng($place_id): Array{
        $key = config('services.google_cloud.key');
            $data = Http::get('https://maps.googleapis.com/maps/api/place/details/json', [
                'language' => 'it',
                'key' => $key,
                'place_id' => $place_id
            ]);
            
           $data = json_decode($data);
            if(!empty($data->result->geometry->location->lat)){
                return ['lat' => $data->result->geometry->location->lat, 'lng' => $data->result->geometry->location->lng];
            }else{
                return null;
            }
    }
}
