<?php

namespace App\Services;

use Exception;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DatabaseBackupService
{
    public function exportMariaDbBackup(): StreamedResponse
    {
        $connection = config('database.default');
        $config = config("database.connections.{$connection}");

        if ($connection !== 'mysql' && $connection !== 'mariadb') {
            throw new Exception('La exportación directa está configurada exclusivamente para MariaDB/MySQL.');
        }

        $host = $config['host'] ?? '127.0.0.1';
        $port = $config['port'] ?? '3306';
        $database = $config['database'];
        $username = $config['username'];
        $password = $config['password'];

        $filename = 'backup_' . $database . '_' . date('Y-m-d_H-i-s') . '.sql';

        return new StreamedResponse(function () use ($host, $port, $database, $username, $password) {
            $command = sprintf(
                'mysqldump --host=%s --port=%s --user=%s --password=%s --single-transaction --routines --triggers %s',
                escapeshellarg($host),
                escapeshellarg($port),
                escapeshellarg($username),
                escapeshellarg($password),
                escapeshellarg($database)
            );

            $handle = popen($command, 'r');
            if ($handle) {
                while (!feof($handle)) {
                    echo fread($handle, 1024);
                    flush();
                }
                pclose($handle);
            }
        }, 200, [
            'Content-Type' => 'application/sql',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}