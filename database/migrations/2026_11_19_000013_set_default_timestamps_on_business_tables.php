<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private array $excludedTables = [
        'cache',
        'cache_locks',
        'failed_jobs',
        'job_batches',
        'jobs',
        'migrations',
        'password_reset_admins',
        'password_reset_users',
        'personal_access_tokens',
        'sessions',
        'wilayah_dati',
        'wilayah_desa_net',
        'wilayah_kabupaten_net',
        'wilayah_kecamatan_net',
        'wilayah_provinsi_net',
    ];

    public function up(): void
    {
        foreach ($this->businessTables() as $tableName) {
            $columns = collect(DB::select('SHOW COLUMNS FROM '.$this->quoteIdentifier($tableName)))
                ->keyBy('Field');

            if ($columns->has('created_at')) {
                $this->setColumnDefault($tableName, $columns->get('created_at'), false);
            }

            if ($columns->has('updated_at')) {
                $this->setColumnDefault($tableName, $columns->get('updated_at'), true);
            }

            if ($columns->has('deleted_at')) {
                DB::statement('ALTER TABLE '.$this->quoteIdentifier($tableName)
                    .' MODIFY COLUMN '.$this->quoteIdentifier('deleted_at')
                    .' '.$columns->get('deleted_at')->Type
                    .' NULL DEFAULT NULL');
            }
        }
    }

    public function down(): void
    {
        // Defaults are intentionally kept during rollback. Removing them can
        // make direct database inserts fail unexpectedly.
    }

    private function businessTables(): array
    {
        return collect(DB::select(
            'SELECT TABLE_NAME FROM information_schema.TABLES
             WHERE TABLE_SCHEMA = ? AND TABLE_TYPE = \'BASE TABLE\'',
            [DB::getDatabaseName()]
        ))
            ->pluck('TABLE_NAME')
            ->reject(fn (string $table): bool => in_array($table, $this->excludedTables, true))
            ->values()
            ->all();
    }

    private function setColumnDefault(string $tableName, object $column, bool $autoUpdate): void
    {
        $sql = 'ALTER TABLE '.$this->quoteIdentifier($tableName)
            .' MODIFY COLUMN '.$this->quoteIdentifier($column->Field)
            .' '.$column->Type
            .' NULL DEFAULT CURRENT_TIMESTAMP';

        if ($autoUpdate) {
            $sql .= ' ON UPDATE CURRENT_TIMESTAMP';
        }

        DB::statement($sql);
    }

    private function quoteIdentifier(string $identifier): string
    {
        return '`'.str_replace('`', '``', $identifier).'`';
    }
};
