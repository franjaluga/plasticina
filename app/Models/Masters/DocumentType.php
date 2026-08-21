<?php

namespace App\Models\Masters;

use Illuminate\Database\Eloquent\Model;

class DocumentType extends Model
{
    public $timestamps = false;
    
    protected $fillable = [
        'doctype',
        'name',
    ];
}