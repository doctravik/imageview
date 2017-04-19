<?php

namespace App\ActivationToken;

use App\ActivationToken;

trait InteractWithActivationToken
{
    /**
     * Check if user has activated account.
     * 
     * @return boolean
     */
    public function isActive()
    {
        return $this->active == true;
    }

    /**
     * Activate user.
     * 
     * @return boolean
     */
    public function activate()
    {
        $this->update(['active' => true]);
    }

    /**
     * User has one Activation token
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function activationToken()
    {
        return $this->hasOne(ActivationToken::class);
    }

    /**
     * Generate activation token.
     * 
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function generateActivationToken()
    {
        if($this->fresh()->activationToken) {
            return $this->activationToken;
        }

        return $this->activationToken()->create([
            'token' => str_random(60)
        ]);
    }
}