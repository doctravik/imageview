<?php

namespace App;

use App\User;
use App\Photo;
use Illuminate\Database\Eloquent\Model;

class Album extends Model
{
    protected $fillable = ['name', 'user_id'];

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
     * Get url.
     * 
     * @return string
     */
    public function url()
    {
        return url('/admin/albums/' . $this->slug);
    }

    /**
     * Album belongs to User.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);    
    }

    /**
     * Album has many photos.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function photos()
    {
        return $this->hasMany(Photo::class);    
    }

    /**
     * Album has many thumbnails through photos.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function thumbnails()
    {
        return $this->hasManyThrough(Thumbnail::class, Photo::class);
    }

    /**
     * Add Photo to album.
     * 
     * @param Photo $photo
     * @return Photo
     */
    public function addPhoto(Photo $photo)
    {
        return $this->photos()->save($photo);
    }

    /**
     * Reset avatars property of the all photos in the album.
     * 
     * @return void
     */
    public function resetAvatars()
    {
        $res = \DB::table('photos')->where('album_id', $this->id)->update(['is_avatar' => false]);
    }
}
