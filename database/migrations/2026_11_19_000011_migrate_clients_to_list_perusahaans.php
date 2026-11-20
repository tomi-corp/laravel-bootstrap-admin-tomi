<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('list_perusahaans')) {
            return;
        }

        Schema::table('list_perusahaans', function (Blueprint $table): void {
            if (! Schema::hasColumn('list_perusahaans', 'legacy_client_id')) {
                $table->unsignedBigInteger('legacy_client_id')->nullable()->unique();
            }
            if (! Schema::hasColumn('list_perusahaans', 'pic_position')) {
                $table->string('pic_position', 100)->nullable();
            }
            if (! Schema::hasColumn('list_perusahaans', 'bank_id')) {
                $table->unsignedBigInteger('bank_id')->nullable();
            }
            foreach ([
                'president_commissioner',
                'commissioner',
                'president_director',
                'director_1',
                'director_2',
                'director_3',
            ] as $column) {
                if (! Schema::hasColumn('list_perusahaans', $column)) {
                    $table->string($column, 255)->nullable();
                }
            }
            if (! Schema::hasColumn('list_perusahaans', 'rate')) {
                $table->decimal('rate', 5, 2)->nullable();
            }
            if (! Schema::hasColumn('list_perusahaans', 'admin_fee')) {
                $table->decimal('admin_fee', 15, 2)->default(0);
            }
        });

        // Legacy client rows may not have the fields that were mandatory in
        // list_perusahaans. Keep those values nullable instead of inventing
        // business data or invalid region IDs.
        DB::statement('ALTER TABLE `list_perusahaans`
            MODIFY `pic_internal` varchar(255) NULL,
            MODIFY `id_provinsi` int NULL,
            MODIFY `id_kabupaten` int NULL,
            MODIFY `id_kecamatan` int NULL,
            MODIFY `id_desa` int NULL,
            MODIFY `alamat_lengkap` text NULL,
            MODIFY `telp` varchar(255) NULL,
            MODIFY `nomor_pks` varchar(255) NULL,
            MODIFY `payment_date` int NULL,
            MODIFY `h_min_jatuh_tempo` int NULL,
            MODIFY `active_profesional_fee` enum(\'active\', \'nonactive\') NULL,
            MODIFY `active_set_pinjaman` enum(\'active\', \'nonactive\') NULL,
            MODIFY `active_tanggal` int NULL');

        if (Schema::hasTable('clients')) {
            DB::transaction(function (): void {
                DB::table('clients')->orderBy('id')->chunkById(500, function ($clients): void {
                    foreach ($clients as $client) {
                        $email = $client->email !== null && trim($client->email) !== ''
                            ? trim($client->email)
                            : null;

                        // list_perusahaans has a unique email. Do not overwrite
                        // an existing company when a legacy email collides.
                        if ($email !== null && DB::table('list_perusahaans')
                            ->where('email_perusahaan', $email)
                            ->where(function ($query) use ($client): void {
                                $query->whereNull('legacy_client_id')
                                    ->orWhere('legacy_client_id', '<>', $client->id);
                            })
                            ->exists()) {
                            $email = null;
                        }

                        $bankCode = $client->bank_id
                            ? DB::table('banks')->where('id', $client->bank_id)->value('code_bank')
                            : null;

                        $values = [
                            'unique_code' => 'CLIENT-'.$client->id,
                            'id_lender' => $client->lender_id,
                            'id_code_perusahaan' => 'CLIENT-'.$client->id,
                            'pic_perusahaan' => $client->person_in_charge ?: $client->name,
                            'pic_internal' => null,
                            'nama' => $client->name,
                            'logo' => $client->logo,
                            'email_perusahaan' => $email,
                            'id_provinsi' => $client->province_id,
                            'id_kabupaten' => $client->city_id,
                            'id_kecamatan' => $client->district_id,
                            'id_desa' => $client->village_id,
                            'alamat_lengkap' => $client->address,
                            'bank' => $bankCode,
                            'bank_id' => $client->bank_id,
                            'no_rekening' => $client->account_number,
                            'atas_nama_rekening' => $client->account_name,
                            'nama_direktur_utama' => $client->president_director,
                            'telp' => $client->phone,
                            'nomor_pks' => null,
                            'payment_date' => null,
                            'h_min_jatuh_tempo' => null,
                            'status' => (int) $client->status === 1 ? 'active' : 'nonactive',
                            'active_profesional_fee' => null,
                            'active_set_pinjaman' => null,
                            'active_tanggal' => null,
                            'pic_position' => $client->pic_position,
                            'president_commissioner' => $client->president_commissioner,
                            'commissioner' => $client->commissioner,
                            'president_director' => $client->president_director,
                            'director_1' => $client->director_1,
                            'director_2' => $client->director_2,
                            'director_3' => $client->director_3,
                            'rate' => $client->rate,
                            'admin_fee' => $client->admin_fee,
                            'legacy_client_id' => $client->id,
                            'created_at' => $client->created_at,
                            'updated_at' => $client->updated_at,
                        ];

                        DB::table('list_perusahaans')->updateOrInsert(
                            ['legacy_client_id' => $client->id],
                            $values
                        );
                    }
                });
            });
        }

        if (Schema::hasTable('financing_request_invoices')
            && ! Schema::hasColumn('financing_request_invoices', 'company_id')) {
            Schema::table('financing_request_invoices', function (Blueprint $table): void {
                $table->unsignedBigInteger('company_id')->nullable()->after('client_id');
                $table->index('company_id', 'idx_financing_request_company_id');
            });
        }

        if (Schema::hasTable('financing_request_invoices')
            && Schema::hasColumn('list_perusahaans', 'legacy_client_id')) {
            DB::statement('UPDATE `financing_request_invoices` AS fri
                INNER JOIN `list_perusahaans` AS lp ON lp.`legacy_client_id` = fri.`client_id`
                SET fri.`company_id` = lp.`id`
                WHERE fri.`company_id` IS NULL');

            if (! $this->hasForeignKey('financing_request_invoices', 'fk_financing_request_company')) {
                Schema::table('financing_request_invoices', function (Blueprint $table): void {
                    $table->foreign('company_id', 'fk_financing_request_company')
                        ->references('id')->on('list_perusahaans')
                        ->nullOnDelete()->cascadeOnUpdate();
                });
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('financing_request_invoices')
            && $this->hasForeignKey('financing_request_invoices', 'fk_financing_request_company')) {
            Schema::table('financing_request_invoices', function (Blueprint $table): void {
                $table->dropForeign('fk_financing_request_company');
            });
        }

        if (Schema::hasTable('financing_request_invoices')
            && Schema::hasColumn('financing_request_invoices', 'company_id')) {
            Schema::table('financing_request_invoices', function (Blueprint $table): void {
                $table->dropIndex('idx_financing_request_company_id');
                $table->dropColumn('company_id');
            });
        }

        if (Schema::hasTable('list_perusahaans')) {
            Schema::table('list_perusahaans', function (Blueprint $table): void {
                foreach ([
                    'legacy_client_id', 'pic_position', 'bank_id', 'president_commissioner',
                    'commissioner', 'president_director', 'director_1', 'director_2',
                    'director_3', 'rate', 'admin_fee',
                ] as $column) {
                    if (Schema::hasColumn('list_perusahaans', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }
    }

    private function hasForeignKey(string $table, string $constraint): bool
    {
        return DB::table('information_schema.TABLE_CONSTRAINTS')
            ->where('CONSTRAINT_SCHEMA', DB::getDatabaseName())
            ->where('TABLE_NAME', $table)
            ->where('CONSTRAINT_NAME', $constraint)
            ->where('CONSTRAINT_TYPE', 'FOREIGN KEY')
            ->exists();
    }
};
