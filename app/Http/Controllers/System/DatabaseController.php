<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use App\Services\DatabaseBackupService;
use Exception;

class DatabaseController extends Controller
{
    protected DatabaseBackupService $backupService;

    public function __construct(DatabaseBackupService $backupService)
    {
        $this->backupService = $backupService;
    }

    public function export()
    {
        try {
            return $this->backupService->exportMariaDbBackup();
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}