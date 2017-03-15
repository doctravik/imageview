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
    private function getResizeParameters($image, $width, $height)
    {
        if($this->hasPortraitOrientation($image)) {
            return [null, $height];
        }

        return [$width, null];
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
}