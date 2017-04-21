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
    
    protected $fillable = ['name', 'description', 'link', 'path', 'is_public', 'is_avatar'];

    /**
    * Get the route key for the model.
    *
    * @return string
    */
    public function getRouteKeyName()
    {
        return 'slug';
    }
    
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

    /**
     * Check if photo is public.
     * 
     * @return boolean
     */
    public function isPublic()
    {
        return $this->is_public;
    }

    /**
     * Make photo public.
     * 
     * @return void
     */
    public function makePublic()
    {
        $this->is_public = true;
    }

    /**
     * Check if photo is avatar.
     * 
     * @return boolean
     */
    public function isAvatar()
    {
        return $this->is_avatar;
    }

    /**
     * Toggle avatar property.
     * 
     * @return void
     */
    public function toggleAvatar()
    {
        $this->is_avatar = !$this->is_avatar;

        if($this->isAvatar()) {
            $this->makePublic();
        }

        $this->save();

        return $this;
    }

    /**
     * Set photo order.
     * 
     * @param int $order
     * @return void
     */
    public function setOrder($order)
    {
        $this->timestamps = false;
        
        $this->sort_order = $order;
        
        $this->save();
    }
}
