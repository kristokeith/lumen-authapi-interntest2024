<?php

namespace App\Transformers;

use App\Models\User;
use League\Fractal\TransformerAbstract;

class UserTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @param  \App\Models\User  $user
     * @return array
     */
    public function transform(User $user): array
    {
        return [
            'uuid' => (string) $user->uuid,
            'name' => (string) $user->name,
            'email' => (string) $user->email,
            'permissions' => $user->getAllPermissions()->pluck('name')->toArray(),
            'profile_photo' => (string) $user->profile_photo,
            'roles' => $user->getRoleNames()->toArray(),
            'username' => (string) $user->username,
            'staff' => (string) $user->staff,
        ];
    }
}
