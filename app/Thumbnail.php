<?php

namespace App;

use App\Photo;
use Carbon\Carbon;
use Intervention\Image\Facades\Image;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use App\Traits\InteractWithInterventionImage;

class Thumbnail extends Model
{
    use InteractWithInterventionImage;

    protected $fillable = ['name', 'path'];
    
    /**
     * Thumbnail belongs to Photo.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function photo()
    {
        return $this->belongsTo(Photo::class);    
    }

    /**
     * Make Thumbnail from Photo.
     * 
     * @param  Photo $photo
     * @return static
     */
    public static function make(Photo $photo)
    {
        $thumbnail = static::createFromPhoto($photo);

        $thumbnail->copy($photo->path)->resize();

        return $thumbnail->associateWith($photo);
    }

    /**
     * Copy file.
     * 
     * @param  string $source
     * @return $this
     */
    public function copy($source)
    {
        Storage::copy($source, $this->path);

        return $this;
    }

    /**
     * Resize thumbnail image.
     * 
     * @return boolean
     */
    public function resize()
    {
        $image = Image::make(Storage::get($this->path));

        list($width, $height) = $this->getResizeParameters($image, $this->width, $this->height);

        $image->resize($width, $height, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });

        return Storage::put($this->path, (string) $image->encode());
    }

    /**
     * Associate thumbnail with the photo.
     * 
     * @param  Photo $photo
     * @return $this
     */
    public function associateWith(Photo $photo)
    {
        $this->photo_id = $photo->id;

        return $this;
    }

    /**
     * Create all thumbnails in one query.
     * 
     * @param  array $attributes
     * @return boolean
     */
    public static function createAll($attributes)
    {
        return \DB::table('thumbnails')->insert($attributes);
    }

    /**
     * Create a new Thumbnail instance from the given Photo.
     * 
     * @param  Photo $photo
     * @param  integer $width
     * @param  integer $height
     * @return static
     */
    public static function createFromPhoto(Photo $photo, $width = 400, $height = 400)
    {
        $name = sprintf("%s_%sx%s.%s", 
            pathinfo($photo->path, PATHINFO_FILENAME), 
            $width, $height, 
            pathinfo($photo->path, PATHINFO_EXTENSION)
        );

        $thumbnail = new static();
        $thumbnail->name = $name;
        $thumbnail->width = $width;
        $thumbnail->height = $height;
        $thumbnail->path = pathinfo($photo->path, PATHINFO_DIRNAME) . '/' . $name;
        $thumbnail->slug  = str_slug(pathinfo($name, PATHINFO_FILENAME));
        $thumbnail->created_at = Carbon::now();
        $thumbnail->updated_at = Carbon::now();

        return $thumbnail;
    }
}
