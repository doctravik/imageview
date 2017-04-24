<?php

namespace App;

use App\User;
use App\Photo;
use Illuminate\Database\Eloquent\Model;

class Album extends Model
{
    protected $fillable = ['name', 'user_id', 'public'];

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
     * Album has many public photos.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function publicPhotos()
    {
        return $this->photos()->where('is_public', true);    
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
     * Album has one avatar
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function avatar()
    {
        return $this->hasOne(Photo::class)->where('is_avatar', true);
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

    /**
     * Check if the album is active.
     * 
     * @return boolean
     */
    public function isPublic()
    {
        return $this->public;
    }
}
