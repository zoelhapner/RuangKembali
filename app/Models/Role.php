<?php

namespace App\Models;

use App\Traits\HasUuid;
use Spatie\Permission\Models\Role as SpatieRole;


class Role extends SpatieRole
{
    use HasUuid;

    protected $keyType = 'string';
    public $incrementing = false;

        public function getRouteKeyName()
    {
        return 'id'; 
    }

    protected $fillable = [
        'name',
        'guard_name',
        'role_group',
    ];

        public function scopeInternal($query)
    {
        return $query->where('role_group', 'Internal');
    }

    public function scopeExternal($query)
    {
        return $query->where('role_group', 'Eksternal');
    }
}
