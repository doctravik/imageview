<?php

namespace App\Image;

use App\Image\ImageHandler;
use Intervention\Image\Facades\Image;

class InterventionImage implements ImageHandler
{
    protected $image;

    /**
     * Make image instance.
     * 
     * @param  mixed $source
     * @return \Intervention\Image\Image
     */
    public function make($source)
    {
        $this->image = Image::make($source);

        return $this;
    }

    public function getImage()
    {
        return $this->image;    
    }

    /**
     * Resize image.
     * 
     * @param  \Intervention\Image\Image $image
     * @param  integer $width
     * @param  integer $height
     * @return \Intervention\Image\Image 
     */
    public function resize($width, $height)
    {
        return $this->image->resize($width, $height, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });
    }

    public function width()
    {
        return $this->image->width();
    }

    public function height()
    {
        return $this->image->height();
    }
}