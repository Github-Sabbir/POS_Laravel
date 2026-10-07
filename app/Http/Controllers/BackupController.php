<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

class BackupController extends Controller
{
    public function download(): StreamedResponse
    {
        $filename = 'retail_pos_backup_' . now()->format('Y-m-d_H-i-s') . '.sql';

        return response()->streamDownload(function () {
            echo "-- Retail POS MySQL backup\n";
            echo "-- Generated: " . now()->toDateTimeString() . "\n\n";
            echo "SET FOREIGN_KEY_CHECKS=0;\nSET SQL_MODE='NO_AUTO_VALUE_ON_ZERO';\n\n";

            $tables = DB::select('SHOW TABLES');
            $databaseKey = 'Tables_in_' . DB::getDatabaseName();
            foreach ($tables as $row) {
                $table = $row->{$databaseKey} ?? array_values((array) $row)[0] ?? null;
                if (!$table || str_starts_with($table, 'sqlite_')) continue;

                echo "DROP TABLE IF EXISTS `" . str_replace('`', '``', $table) . "`;\n";
                $create = DB::select('SHOW CREATE TABLE `' . str_replace('`', '``', $table) . '`');
                if ($create) {
                    $createRow = (array) $create[0];
                    $createSql = $createRow['Create Table'] ?? end($createRow) ?? '';
                    echo $createSql . ";\n\n";
                }

                $rows = DB::table($table)->get();
                if ($rows->isNotEmpty()) {
                    $columns = array_keys((array) $rows->first());
                    $columnSql = implode(', ', array_map(fn($c) => '`' . str_replace('`', '``', $c) . '`', $columns));
                    foreach ($rows as $data) {
                        $values = [];
                        foreach ($columns as $column) {
                            $value = $data->{$column};
                            if ($value === null) {
                                $values[] = 'NULL';
                            } elseif (is_bool($value)) {
                                $values[] = $value ? '1' : '0';
                            } else {
                                $values[] = DB::getPdo()->quote((string) $value);
                            }
                        }
                        echo "INSERT INTO `{$table}` ({$columnSql}) VALUES (" . implode(', ', $values) . ");\n";
                    }
                    echo "\n";
                }
            }
            echo "SET FOREIGN_KEY_CHECKS=1;\n";
        }, $filename, ['Content-Type' => 'application/sql; charset=UTF-8']);
    }
    public function restore(\Illuminate\Http\Request $request): \Illuminate\Http\RedirectResponse
    {
        abort_unless($request->user()?->role === 'admin', 403);

        $data = $request->validate([
            'backup' => 'required|file|extensions:sql,txt|max:51200',
        ]);

        $sql = file_get_contents($data['backup']->getRealPath());
        if ($sql === false || trim($sql) === '') {
            return back()->withErrors(['backup' => 'The backup file is empty or could not be read.']);
        }

        // This application generates plain MySQL dumps. Restore is intentionally
        // restricted to Admin because it can replace the whole database.
        try {
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
            DB::unprepared($sql);
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        } catch (\Throwable $e) {
            try { DB::statement('SET FOREIGN_KEY_CHECKS=1'); } catch (\Throwable $ignored) {}
            report($e);
            return back()->withErrors(['backup' => 'Backup restore failed. Please verify that this is a Retail POS MySQL .sql backup.']);
        }

        return back()->with('success', 'Database backup restored successfully. Please refresh the application.');
    }

}
