<?php

namespace App;

use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Photo extends Model
{
    protected $fillable = ['name', 'description', 'link'];


    /**
     * Photo belongs to user.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);        
    }

    /**
     * Upload photos.
     * 
     * @param  array $files
     * @return void
     */
    public static function upload($files)
    {
        $photos = array_reduce($files, function($carry, $file) {
            $carry[] = [
                'path'        => $file->store('images'),
                'user_id'     => auth()->user()->id,
                'created_at'  => Carbon::now(),
                'updated_at'  => Carbon::now()
            ];

            return $carry;
        }, []);

        static::persist($photos);
    }

    /**
     * Persist photos in the database.
     * 
     * @param  array $photos
     * @return void
     */
    public static function persist($photos)
    {
        DB::table('photos')->insert($photos);
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
