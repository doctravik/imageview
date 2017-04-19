<?php

namespace App;

use App\User;
use Illuminate\Database\Eloquent\Model;

class ActivationToken extends Model
{
    protected $fillable = ['token'];

    /**
     * Activation token belongs to user.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
