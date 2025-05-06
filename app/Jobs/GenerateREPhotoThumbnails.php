<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\RealEstateProperty;
use App\Models\RealEstatePropertyPhoto;
use Illuminate\Support\Facades\Storage;

class GenerateREPhotoThumbnails implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $re_photo;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(RealEstatePropertyPhoto $re_photo)
    {
        $this->re_photo = $re_photo;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $pi = pathinfo($this->re_photo->path);

        if(empty($this->re_photo->path) || Storage::disk('s3')->missing($this->re_photo->path)){
            return false;
        }

        $thumb_80_path = $pi['dirname'].'/'.$pi['filename'].'_80x80.'.$pi['extension']; 
        $thumb_300_path = $pi['dirname'].'/'.$pi['filename'].'_300x300.'.$pi['extension']; 

        $thumbnail_80 = \Thumbnail::src(Storage::disk('s3')->url($this->re_photo->path))->smartcrop(80, 80)->string();
        Storage::disk('s3')->put($thumb_80_path, $thumbnail_80);

        $thumbnail_300 = \Thumbnail::src(Storage::disk('s3')->url($this->re_photo->path))->smartcrop(300, 300)->string();
        Storage::disk('s3')->put($thumb_300_path, $thumbnail_300);

        $this->re_photo->thumb_80_path = $thumb_80_path;
        $this->re_photo->thumb_300_path = $thumb_300_path;

        $this->re_photo->save();
        return $this->re_photo;
    }
}
