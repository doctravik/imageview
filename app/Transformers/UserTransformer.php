<?php

namespace App\Transformers;

use App\User;
use League\Fractal\TransformerAbstract;

class UserTransformer extends TransformerAbstract
{
    /**
     * Turn this User object into a generic array.
     *
     * @return array
     */
    public function transform(User $user)
    {
        return [
            // 'id' => (int) $user->id,
            'name' => $user->name
        ];
    }
}