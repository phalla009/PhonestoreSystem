<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RolePermission extends Model
{
    protected $table = "roles_permissions";
    protected $id = "id";
    protected $fillable = [
        'roles_id',
        'permissions_id'
    ];
    public static function addRolePermissions($role_id, $permisson_ids=[]) {
        foreach($permisson_ids as $permisson_id){
            RolePermission::create([
                'roles_id' => $role_id,
                'permissions_id' => $permisson_id
            ]);
        }
    }
}
