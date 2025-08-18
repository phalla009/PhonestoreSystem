<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $fillable = ['role_name', 'description'];

    /**
     * Permissions relationship (many-to-many)
     */
    public function permissions()
    {
        return $this->belongsToMany(
            Permission::class,
            'roles_permissions', // pivot table
            'roles_id',          // foreign key on pivot for Role
            'permissions_id'     // foreign key on pivot for Permission
        )->withTimestamps();
    }

    /**
     * Assign multiple permissions
     */
    public function assignPermissions(array $permissionIds)
    {
        $this->permissions()->sync($permissionIds);
    }

    /**
     * Remove all permissions
     */
    public function removeAllPermissions()
    {
        $this->permissions()->detach();
    }

    /**
     * Check if the role has a permission
     */
    public function hasPermission($slug)
    {
        return $this->permissions()->where('slug', $slug)->exists();
    }
}
