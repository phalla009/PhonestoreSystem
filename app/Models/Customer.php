<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $table = 'customers';
    protected $fillable = ['name', 'gender','phone', 'status'];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function getStatusAttribute()
    {
        return $this->orders()->count() > 0 ? 'Active' : 'Inactive';
    }
}
