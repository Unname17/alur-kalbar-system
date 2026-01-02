<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $connection = 'sistem_admin';
    protected $table = 'roles';

    protected $fillable = ['name', 'display_name'];

    public function users()
    {
        return $this->hasMany(User::class, 'role_id');
    }
}