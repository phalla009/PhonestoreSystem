<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $table = 'permissions';
    protected $primaryKey = 'id';
    protected $fillable = [
        'permission_name',
        'slug',
        'permission_group'
    ];

    public static function getAllPermissions() {
        $allPermissions = [];
        $permissionGroups = Permission::groupBy('permission_group')->get();
        foreach($permissionGroups as $permissionGroup ) {
            $permissionsOfAGroup = Permission::where('permission_group', '=', $permissionGroup->permission_group)->get();
            $allPermissions[$permissionGroup->permission_name] = $permissionsOfAGroup;
            $permissionsOfAGroup = '';
        }
        return $allPermissions;
    }
}
