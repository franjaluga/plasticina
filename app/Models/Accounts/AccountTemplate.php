<?php

namespace App\Models\Accounts;

use Illuminate\Database\Eloquent\Model;

class AccountTemplate extends Model
{
    protected $fillable = ['slug', 'name', 'description'];

    public function items()
    {
        return $this->hasMany(AccountTemplateItem::class);
    }
}