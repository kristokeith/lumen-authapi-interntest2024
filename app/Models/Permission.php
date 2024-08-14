<?php

namespace App\Models;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Models\Permission as SpatiePermission;

class Permission extends SpatiePermission
{
    use HasFactory, UuidTrait, SoftDeletes;
    protected $fillable = ['uuid', 'name', 'guard_name'];
    protected $keyType = 'string';
    public $incrementing = false;
    protected $primaryKey = 'uuid';
}
