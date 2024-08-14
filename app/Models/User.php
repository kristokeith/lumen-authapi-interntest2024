<?php

namespace App\Models;

use App\Traits\AttributeHashable;
use App\Traits\ModelValidatable;
use App\Traits\QueryFilterable;
use App\Traits\UuidTrait;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Lumen\Auth\Authorizable;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Model implements AuthenticatableContract, AuthorizableContract, JWTSubject
{
    use Authenticatable, Authorizable, QueryFilterable, ModelValidatable, AttributeHashable, HasFactory, HasRoles, UuidTrait, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['uuid','name', 'username', 'email', 'no_hp', 'password'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];

    protected $primaryKey = 'uuid';

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $visible = [
        'uuid', 'name', 'username', 'email', 'no_hp', 'roles_uuid',
    ];

    /**
     * The fields that should be filterable by query.
     *
     * @var array
     */
    protected $filterable = [
        'name', 'email', 'username',
    ];

    /**
     * Hash the attributes before saving.
     *
     * @var array
     */
    protected $hashable = [
//        'password',
    ];

    /**
     * Validation rules for the model.
     *
     * @return array
     */


    public function getRolesUuidAttribute() {
        return $this->roles->pluck('uuid');
    }

    public function rules(): array
    {
        return [
            '*' => [
                'name' => 'required',
            ],
            'CREATE' => [
                'email' => 'required|unique:users,email',
                'username' => 'required|unique:users,username',
                'password' => 'required|min:6',
            ],
            'UPDATE' => [
                'email' => 'required|unique:users,email,' . $this->uuid,
                'username' => 'required|unique:users,username' . $this->uuid,
                'password' => 'sometimes|min:6',
            ],
        ];
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims(): array
    {
        return [];
    }
}
