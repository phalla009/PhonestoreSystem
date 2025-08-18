<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserManager extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'users';

    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * UserManager belongs to a single role
     */
    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    /**
     * Get all permissions from the user's role as a Collection
     */
    public function permissions()
    {
        return $this->role ? $this->role->permissions : collect();
    }

    /**
     * Check if the user has a specific permission
     */
    public function hasPermission($slug)
    {
        return $this->permissions()->contains('slug', $slug);
    }

    /**
     * Shortcut for checking if the user is admin
     */
    public function isAdmin()
    {
        return $this->role && strtolower($this->role->role_name) === 'admin';
    }
}
