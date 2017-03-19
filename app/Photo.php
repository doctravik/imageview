<?php

namespace App;

use App\Album;
use App\Thumbnail;
use App\Image\UrlHelper;
use Illuminate\Http\UploadedFile;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Photo extends Model
{
    use UrlHelper;
    
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
        $filename = sha1(time() . $file->getClientOriginalName()) . '.' . $file->getClientOriginalExtension();

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
}
