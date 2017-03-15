<?php

namespace App;

use App\Album;
use App\Thumbnail;
use Illuminate\Http\UploadedFile;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Photo extends Model
{
    protected $fillable = ['name', 'description', 'link', 'path'];

    /**
     * Photo belongs to Album.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function album()
    {
        return $this->belongsTo(Album::class);
    }

    /**
     * Photo has many thumbnails.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function thumbnails()
    {
        return $this->hasMany(Thumbnail::class);    
    }

    /**
     * Upload photos.
     * 
     * @param  UploadedFile $file
     * @param  Album $album
     * @return $this
     */
    public static function upload(UploadedFile $file, Album $album = null)
    {
        $filename = sha1(time()) . '.' . $file->getClientOriginalExtension();

        $path = $file->storeAs('images', $filename);

        $photo = static::createFromPath($path);

        return $album ? $album->addPhoto($photo) : $photo->create($photo->toArray());
    }

    /**
     * Create a new Photo instance from the given path.
     * 
     * @param  string $path
     * @return static
     */
    public static function createFromPath($path)
    {
        $photo = new static;
        $photo->path = $path;
        $photo->name = pathinfo($path, PATHINFO_BASENAME);

        return $photo;
    }

    /**
     * Create all photos in one query.
     * 
     * @param  array $attributes
     * @return boolean
     */
    public static function createAll($attributes)
    {
        return \DB::table('photos')->insert($attributes);
    }
    
    /**
     * Get url path to the file.
     * 
     * @return string
     */
    public function url()
    {
        return Storage::url($this->path);
    }
}
