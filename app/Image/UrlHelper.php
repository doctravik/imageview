<?php

namespace App\Image;

use Illuminate\Support\Facades\Storage;

trait UrlHelper
{
    /**
     * Thumbnail map of sizes.
     * 
     * @return \Illuminate\Support\Collection
     */
    public function sizes()
    {
        return collect([
            'small' => 300,
            'medium' => 600,
            'large' => 800,
        ]);
    }
    
    /**
     * Get url to photo.
     * 
     * @return string
     */
    public function url()
    {
        return Storage::url($this->path);
    }

    /**
     * Get url to small photo.
     * 
     * @return string
     */
    public function small()
    {
        return Storage::url($this->path('small'));
    }

    /**
     * Get url to medium photo.
     * 
     * @return string
     */
    public function medium()
    {
        return Storage::url($this->path('medium'));
    }

    /**
     * Get url to large photo.
     * 
     * @return string
     */
    public function large()
    {
        return Storage::url($this->path('large'));
    }

    /**
     * Get path to photo by size.
     * 
     * @param  sting $size
     * @return string
     */
    public function path($size)
    {
        $dirname = pathinfo($this->path, PATHINFO_DIRNAME);
        $filename = pathinfo($this->path, PATHINFO_FILENAME) . '_' . $size;
        $ext = pathinfo($this->path, PATHINFO_EXTENSION);

        return "{$dirname}/{$filename}.{$ext}";
    }
}