<?php

namespace App\Traits;

trait InteractWithInterventionImage
{
    /**
     * Get resize parameters
     * 
     * @param  \Intervention\Image\Image $image
     * @param  integer $width
     * @param  integer $height
     * @return array
     */
    private function getResizeParameters($image, $size)
    {
        $parameter = $this->sizes()[$size];

        if($this->hasPortraitOrientation($image)) {
            return [null, $parameter];
        }

        return [$parameter, null];
    }

    /**
     * Check if image has portrait orientation.
     * 
     * @param \Intervention\Image\Image $image
     * @return boolean
     */
    private function hasPortraitOrientation($image)
    {
        return $image->height() > $image->width();
    }


    private function sizes()
    {
        return [
            'small' => 300,
            'medium' => 600,
            'large' => 800,
        ];
    }
}