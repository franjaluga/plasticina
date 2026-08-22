<?php

namespace App\Models\Accounting;

use Illuminate\Database\Eloquent\Model;
use App\Models\VCDocuments\VCDocument;
use App\Models\Owners\Owner;

class Journal extends Model
{
    protected $table = 'journals';

    protected $fillable = [
        'vc_document_id', 
        'owner_id', 
        'year', 
        'entry_number', 
        'date', 
        'total_debit', 
        'total_credit', 
        'is_balanced'
    ];

    public function entries()
    {
        return $this->hasMany(JournalEntry::class);
    }

    public function document()
    {
        return $this->belongsTo(VCDocument::class, 'vc_document_id');
    }

    public function owner()
    {
        return $this->belongsTo(Owner::class);
    }
}