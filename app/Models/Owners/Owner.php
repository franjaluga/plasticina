<?php

namespace App\Models\Owners;

use Illuminate\Database\Eloquent\Model;

class Owner extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'rut',
        'name',
        'is_active'
    ];
}
