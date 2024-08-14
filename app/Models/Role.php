<?php

namespace App\Models;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    use HasFactory, UuidTrait, SoftDeletes;
    protected $fillable = ['uuid', 'name', 'guard_name', 'is_default_system'];
    protected $keyType = 'string';
    public $incrementing = false;
    protected $primaryKey = 'uuid';

    protected $visible = ['uuid', 'name', 'guard_name','created_at','updated_at','total_permissions'];

    protected $appends = ['total_permissions'];

    public function getTotalPermissionsAttribute()
    {
        return $this->permissions->count();
    }
}
