<?php

namespace App\Models\Owners;

use Illuminate\Database\Eloquent\Model;
use App\Models\Accounts\Account;

class Owner extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'rut',
        'name',
        'is_active',
        'account_plan_type'
    ];

    public function accounts()
    {
        return $this->hasMany(Account::class, 'owner_id');
    }
}