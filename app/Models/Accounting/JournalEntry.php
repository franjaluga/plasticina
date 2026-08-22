<?php

namespace App\Models\Accounting;

use Illuminate\Database\Eloquent\Model;
use App\Models\Accounts\Account;

class JournalEntry extends Model
{
    protected $table = 'journal_entries';

    protected $fillable = [
        'journal_id', 'account_code', 'component_name', 'debit', 'credit'
    ];

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_code', 'code');
    }
}