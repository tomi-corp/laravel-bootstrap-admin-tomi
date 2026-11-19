<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tables managed by Laravel or used as static reference data.
     * They should not participate in business soft-delete behaviour.
     */
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
            $this->addMissingTimestampColumns($tableName);
            $this->moveTimestampColumnsToEnd($tableName);
        }
    }

    public function down(): void
    {
        // Timestamp columns may have existed before this migration. Removing
        // them during rollback could destroy application data, so this
        // schema-normalisation migration is intentionally not destructive.
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

    private function addMissingTimestampColumns(string $tableName): void
    {
        Schema::table($tableName, function (Blueprint $table) use ($tableName): void {
            if (! Schema::hasColumn($tableName, 'created_at')) {
                $table->timestamp('created_at')->nullable();
            }
            if (! Schema::hasColumn($tableName, 'updated_at')) {
                $table->timestamp('updated_at')->nullable();
            }
            if (! Schema::hasColumn($tableName, 'deleted_at')) {
                $table->softDeletes();
            }
        });
    }

    private function moveTimestampColumnsToEnd(string $tableName): void
    {
        $columns = DB::select('SHOW COLUMNS FROM '.$this->quoteIdentifier($tableName));
        $timestampColumns = collect($columns)
            ->filter(fn (object $column): bool => in_array($column->Field, ['created_at', 'updated_at', 'deleted_at'], true))
            ->keyBy('Field');

        if ($timestampColumns->isEmpty()) {
            return;
        }

        $lastColumn = collect($columns)
            ->reject(fn (object $column): bool => in_array($column->Field, ['created_at', 'updated_at', 'deleted_at'], true))
            ->last();
        $after = $lastColumn?->Field;

        foreach (['created_at', 'updated_at', 'deleted_at'] as $columnName) {
            if (! $timestampColumns->has($columnName)) {
                continue;
            }

            $column = $timestampColumns->get($columnName);
            $sql = 'ALTER TABLE '.$this->quoteIdentifier($tableName)
                .' MODIFY COLUMN '.$this->quoteIdentifier($columnName)
                .' '.$column->Type
                .($column->Null === 'YES' ? ' NULL' : ' NOT NULL')
                .$this->defaultClause($column->Default)
                .($column->Extra !== '' ? ' '.$column->Extra : '')
                .' AFTER '.$this->quoteIdentifier($after ?? $columnName);

            DB::statement($sql);
            $after = $columnName;
        }
    }

    private function defaultClause(mixed $default): string
    {
        if ($default === null) {
            return ' DEFAULT NULL';
        }

        if (preg_match('/^CURRENT_TIMESTAMP(?:\\(\\d*\\))?$/i', (string) $default)) {
            return ' DEFAULT '.$default;
        }

        return ' DEFAULT '.DB::getPdo()->quote((string) $default);
    }

    private function quoteIdentifier(string $identifier): string
    {
        return '`'.str_replace('`', '``', $identifier).'`';
    }
};
