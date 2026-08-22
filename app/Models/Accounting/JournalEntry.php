<?php

namespace App\Models\Accounting;

use Illuminate\Database\Eloquent\Model;

class JournalEntry extends Model
{
    protected $table = 'journal_entries';

    protected $fillable = [
        'journal_id', 'account_code', 'component_name', 'debit', 'credit'
    ];
}