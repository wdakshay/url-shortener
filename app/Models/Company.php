<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $fillable = ['name'];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function urls()
    {
        return $this->hasMany(Url::class);
    }

    public function invitations()
    {
        return $this->hasMany(Invitation::class);
    }
}
