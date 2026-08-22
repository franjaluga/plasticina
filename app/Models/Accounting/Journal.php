<?php

namespace App\Models\Accounting;

use Illuminate\Database\Eloquent\Model;
use App\Models\VCDocuments\VCDocument;

class Journal extends Model
{
    protected $table = 'journals';

    protected $fillable = [
        'vc_document_id', 'date', 'total_debit', 'total_credit', 'is_balanced'
    ];

    public function entries()
    {
        return $this->hasMany(JournalEntry::class);
    }

    public function document()
    {
        return $this->belongsTo(VCDocument::class, 'vc_document_id');
    }
}