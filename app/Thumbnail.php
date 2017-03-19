<?php

namespace App;

use App\Image\UrlHelper;
use App\Image\ImageHandler;
use Illuminate\Support\Facades\Storage;

class Thumbnail
{
    use UrlHelper;
    
    /**
     * Path to parent photo.
     * 
     * @var string
     */
    protected $path;

    /**
     * Size of thumbnail.
     * 
     * @var string
     */
    protected $size;

    /**
     * Image handler.
     * 
     * @var App\Image\ImageHandler
     */
    protected $imageHandler;


    public function __construct($path, $size = null)
    {
        $this->path = $path;

        $this->size = $size ?? 'small';

        $this->imageHandler = resolve(ImageHandler::class);
    }

    /**
     * Make instance of Thumbnail.
     * 
     * @param  string $path
     * @param  string|null $size
     * @return Thumbnail
     */
    public static function make($path, $size = null)
    {
        return (new static($path, $size))->makeImage();
    }

    /**
     * Make image.
     * 
     * @return $this
     */
    protected function makeImage()
    {
        $this->imageHandler->make(Storage::get($this->path));

        return $this;
    }

    /**
     * Save thumbnail in the filesystem.
     * 
     * @return $this
     */
    public function save()
    {
        Storage::put($this->path($this->size), (string) $this->image()->encode());

        return $this;
    }

    /**
     * Resize thumbnail.
     * 
     * @param  string|null $size
     * @return $this
     */
    public function resize($size = null)
    {
        if($size) { 
            $this->setSize($size);
        }

        list($width, $height) = $this->getResizeParameters();

        $this->imageHandler->resize($width, $height);

        return $this;
    }

    /**
     * Get resize parameters
     * 
     * @return array
     */
    protected function getResizeParameters()
    {
        $parameter = $this->sizes()->get($this->size);

        if($this->hasPortraitOrientation()) {
            return [null, $parameter];
        }

        return [$parameter, null];
    }

    /**
     * Check if thumbnail has a portrait orientation.
     * 
     * @return boolean
     */
    protected function hasPortraitOrientation()
    {
        return $this->height() > $this->width();
    }

    /**
     * Set thumbnail size.
     * 
     * @param string $size
     * @return void
     */
    protected function setSize($size)
    {
        $this->size = $size;
    }

    /**
     * Get path.
     * 
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Get imageHandler.
     * 
     * @return \App\Image\ImageHandler
     */
    public function getImageHandler()
    {
        return $this->imageHandler;
    }

    /**
     * Get image.
     * 
     * @return mixed
     */
    public function image()
    {
        return $this->imageHandler->getImage();
    }

    /**
     * Get thumbnail width.
     * 
     * @return integer
     */
    public function width()
    {
        return $this->getImageHandler()->width();
    }

    /**
     * Get thumbnail height.
     * 
     * @return integer
     */
    public function height()
    {
        return $this->getImageHandler()->height();
    }
}
