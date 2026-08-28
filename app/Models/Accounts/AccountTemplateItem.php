<?php

namespace App\Models\Accounts;

use Illuminate\Database\Eloquent\Model;

class AccountTemplateItem extends Model
{
    protected $fillable = ['account_template_id', 'code', 'name', 'category'];

    public function template()
    {
        return $this->belongsTo(AccountTemplate::class, 'account_template_id');
    }
}