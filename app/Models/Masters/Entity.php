<?php

namespace App\Models\Masters;

use Illuminate\Database\Eloquent\Model;

class Entity extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'rut',
        'name',
    ];
}