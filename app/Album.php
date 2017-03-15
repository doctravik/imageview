<?php

namespace App;

use App\User;
use App\Photo;
use Illuminate\Database\Eloquent\Model;

class Album extends Model
{
    protected $fillable = ['name', 'user_id'];

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
     * Add Photo to album.
     * 
     * @param Photo $photo
     * @return Photo
     */
    public function addPhoto(Photo $photo)
    {
        return $this->photos()->save($photo);
    }
}
