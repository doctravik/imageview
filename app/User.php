<?php

namespace App;

use App\Album;
use App\Photo;
use App\Traits\InteractWithAlbum;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable, InteractWithAlbum;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * User has many albums.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function albums()
    {
        return $this->hasMany(Album::class);    
    }

    /**
     * User has many photos through albums.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function photos()
    {
        return $this->hasManyThrough(Photo::class, Album::class);   
    }

    /**
     * User has more photos than limit.
     * 
     * @return boolean
     */
    public function isOutOfLimit()
    {
        return $this->photos->count() >= 200;
    }
}
