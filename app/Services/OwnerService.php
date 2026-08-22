<?php

namespace App\Services;

use App\Models\Owners\Owner;

class OwnerService
{
    public function getActiveOwner(): ?Owner
    {
        return Owner::where('is_active', true)->first();
    }
}