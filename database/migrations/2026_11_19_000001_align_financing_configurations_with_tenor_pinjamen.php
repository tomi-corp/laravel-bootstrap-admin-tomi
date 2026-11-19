<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private array $tables = [
        'financing_request_configurations',
        'salary_financing_configurations',
    ];

    public function up(): void
    {
        foreach ($this->tables as $table) {
            if (! Schema::hasColumn($table, 'company_id')) {
                continue;
            }

            $this->dropForeignIfExists($table, 'fk_financing_request_config_company');
            $this->dropForeignIfExists($table, 'fk_financing_request_config_lender');
            $this->dropForeignIfExists($table, 'fk_salary_financing_company');

            foreach ($this->indexesFor($table) as $index) {
                DB::statement("ALTER TABLE `{$table}` DROP INDEX `{$index}`");
            }

            DB::statement("ALTER TABLE `{$table}` CHANGE `company_id` `id_perusahaan` BIGINT UNSIGNED NULL");
            DB::statement("ALTER TABLE `{$table}` DROP COLUMN `lender_id`, DROP COLUMN `provinsi`, DROP COLUMN `iuran_anggota`, DROP COLUMN `deleted_at`");

            if (! Schema::hasColumn($table, 'provisi')) {
                DB::statement("ALTER TABLE `{$table}` ADD `provisi` DOUBLE NOT NULL DEFAULT 0 AFTER `asuransi`");
            }

            DB::statement("ALTER TABLE `{$table}`
                MODIFY `tenor` INT NOT NULL,
                MODIFY `rate` DOUBLE NOT NULL,
                MODIFY `asuransi` DOUBLE NOT NULL,
                MODIFY `provisi` DOUBLE NOT NULL,
                MODIFY `biaya_admin` DOUBLE NOT NULL,
                MODIFY `fee_tomi` DOUBLE NOT NULL,
                MODIFY `biaya_layanan` DOUBLE NULL,
                MODIFY `minimal_nominal` DOUBLE NULL,
                MODIFY `maximal_nominal` DOUBLE NULL,
                MODIFY `profesional_fee_1` DOUBLE NOT NULL DEFAULT 0,
                MODIFY `profesional_fee_2` DOUBLE NOT NULL DEFAULT 0,
                MODIFY `ksp` DOUBLE NOT NULL DEFAULT 0,
                MODIFY `created_at` TIMESTAMP NULL,
                MODIFY `updated_at` TIMESTAMP NULL");

            DB::statement("ALTER TABLE `{$table}` COLLATE = utf8mb4_general_ci");
            DB::statement("ALTER TABLE `{$table}` ADD INDEX `{$table}_id_perusahaan_index` (`id_perusahaan`)");
            DB::statement("ALTER TABLE `{$table}` ADD CONSTRAINT `{$table}_id_perusahaan_fk` FOREIGN KEY (`id_perusahaan`) REFERENCES `list_perusahaans` (`id`)");
        }
    }

    public function down(): void
    {
        foreach ($this->tables as $table) {
            if (! Schema::hasColumn($table, 'id_perusahaan')) {
                continue;
            }

            $this->dropForeignIfExists($table, "{$table}_id_perusahaan_fk");
            DB::statement("ALTER TABLE `{$table}` DROP INDEX `{$table}_id_perusahaan_index`");
            DB::statement("ALTER TABLE `{$table}` CHANGE `id_perusahaan` `company_id` BIGINT UNSIGNED NOT NULL");
            DB::statement("ALTER TABLE `{$table}`
                ADD `lender_id` BIGINT UNSIGNED NOT NULL AFTER `company_id`,
                ADD `provinsi` VARCHAR(255) NULL AFTER `asuransi`,
                ADD `iuran_anggota` DECIMAL(15,2) NOT NULL DEFAULT 0 AFTER `ksp`,
                ADD `deleted_at` TIMESTAMP NULL AFTER `updated_at`");

            DB::statement("ALTER TABLE `{$table}`
                MODIFY `tenor` SMALLINT UNSIGNED NOT NULL,
                MODIFY `rate` DECIMAL(5,2) NOT NULL DEFAULT 0.00,
                MODIFY `asuransi` DECIMAL(5,2) NOT NULL DEFAULT 0.00,
                MODIFY `biaya_admin` DECIMAL(5,2) NOT NULL DEFAULT 0.00,
                MODIFY `fee_tomi` DECIMAL(5,2) NOT NULL DEFAULT 0.00,
                MODIFY `profesional_fee_1` DECIMAL(5,2) NOT NULL DEFAULT 0.00,
                MODIFY `profesional_fee_2` DECIMAL(5,2) NOT NULL DEFAULT 0.00,
                MODIFY `ksp` DECIMAL(5,2) NOT NULL DEFAULT 0.00,
                MODIFY `biaya_layanan` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
                MODIFY `minimal_nominal` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
                MODIFY `maximal_nominal` DECIMAL(15,2) NOT NULL DEFAULT 0.00");

            DB::statement("ALTER TABLE `{$table}` DROP COLUMN `provisi`");
            DB::statement("ALTER TABLE `{$table}` COLLATE = utf8mb4_unicode_ci");
        }
    }

    private function dropForeignIfExists(string $table, string $foreign): void
    {
        $exists = collect(Schema::getForeignKeys($table))
            ->contains(fn (array $key) => ($key['name'] ?? null) === $foreign);

        if ($exists) {
            Schema::table($table, function (Blueprint $blueprint) use ($foreign): void {
                $blueprint->dropForeign($foreign);
            });
        }
    }

    private function indexesFor(string $table): array
    {
        $known = [
            'financing_request_configurations' => [
                'idx_financing_request_config_company',
                'idx_financing_request_config_lender',
                'idx_financing_request_config_company_tenor',
            ],
            'salary_financing_configurations' => [
                'idx_company_tenor',
            ],
        ];

        return array_values(array_filter(
            $known[$table],
            fn (string $index) => DB::table('information_schema.statistics')
                ->where('table_schema', DB::raw('DATABASE()'))
                ->where('table_name', $table)
                ->where('index_name', $index)
                ->exists(),
        ));
    }
};
