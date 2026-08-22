<?php

namespace App\Models\VCDocuments;

use Illuminate\Database\Eloquent\Model;
use App\Models\Accounting\Journal;

class VCDocument extends Model
{
    protected $table = 'vc_documents';
    
    public $timestamps = false;

    protected $fillable = [
        'month_register',
        'year_register',
        'type_vc',
        'entity_id',
        'document_type_id',
        'folio',
        'date',
        'rut_ref',
        'folio_ref',
        'td_ref',
        'date_centralize',
        'net',
        'exempt',
        'vat_rec',
        'vat_no_rec',
        'plus_oth_tax',
        'minus_oth_tax',
        'total',
        'owner_id'
    ];

    public function journal()
    {
        return $this->hasOne(Journal::class, 'vc_document_id');
    }
}
