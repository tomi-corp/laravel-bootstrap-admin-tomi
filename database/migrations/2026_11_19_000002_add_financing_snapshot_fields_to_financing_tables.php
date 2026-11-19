<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private array $tables = [
        'financing_request_invoices',
        'salary_financings',
    ];

    private array $rateFields = [
        'rate',
        'asuransi',
        'provisi',
        'biaya_admin',
        'fee_tomi',
        'biaya_layanan',
        'profesional_fee_1',
        'profesional_fee_2',
        'ksp',
    ];

    public function up(): void
    {
        foreach ($this->tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName): void {
                foreach ($this->rateFields as $field) {
                    $table->decimal($field, 8, 2)
                        ->nullable()
                        ->comment("Snapshot {$field} saat transaksi dibuat")
                        ->after($this->afterColumn($field));

                    $table->decimal("{$field}_amount", 18, 2)
                        ->nullable()
                        ->comment("Nominal snapshot {$field} saat transaksi dibuat")
                        ->after($field);
                }
            });
        }
    }

    public function down(): void
    {
        foreach ($this->tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table): void {
                foreach ($this->rateFields as $field) {
                    $table->dropColumn([$field, "{$field}_amount"]);
                }
            });
        }
    }

    private function afterColumn(string $field): string
    {
        return match ($field) {
            'rate' => 'updated_at',
            default => $this->rateFields[array_search($field, $this->rateFields, true) - 1] . '_amount',
        };
    }
};
