<?php

namespace App\Models\Accounts;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Services\OwnerService;

class Account extends Model
{
    use HasFactory;

    protected $table = 'accounts';

    protected $fillable = [
        'owner_id',
        'code', 
        'name', 
        'category'
    ];

    public static function getActiveOwnerAccounts()
    {
        $activeOwner = app(OwnerService::class)->getActiveOwner();

        if (!$activeOwner) {
            return collect();
        }

        return self::where('owner_id', $activeOwner->id)
            ->orderBy('code', 'asc')
            ->get()
            ->unique('code');
    }
}