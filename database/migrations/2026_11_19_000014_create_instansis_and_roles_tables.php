<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('instansis', function (Blueprint $table): void {
            $table->id();
            $table->string('code', 50)->unique();
            $table->string('name', 100);
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('roles', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('instansi_id')->constrained('instansis')->cascadeOnDelete();
            $table->string('code', 50);
            $table->string('name', 100);
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['instansi_id', 'code']);
        });

        $instansis = [
            ['code' => 'internal', 'name' => 'Internal'],
            ['code' => 'lender', 'name' => 'Lender'],
            ['code' => 'perusahaan', 'name' => 'Perusahaan'],
            ['code' => 'referral', 'name' => 'Referral'],
        ];

        foreach ($instansis as $instansi) {
            DB::table('instansis')->insert([
                ...$instansi,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $roleMap = [
            'internal' => ['super_admin', 'admin'],
            'lender' => ['finance', 'analyst', 'approver'],
            'perusahaan' => ['hrd', 'finance', 'boss'],
            'referral' => ['referral'],
        ];

        foreach ($roleMap as $instansiCode => $roles) {
            $instansiId = DB::table('instansis')->where('code', $instansiCode)->value('id');

            foreach ($roles as $role) {
                DB::table('roles')->insert([
                    'instansi_id' => $instansiId,
                    'code' => $role,
                    'name' => ucwords(str_replace('_', ' ', $role)),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        Schema::table('admins', function (Blueprint $table): void {
            $table->foreignId('instansi_id')->nullable()->after('id');
            $table->foreignId('role_id')->nullable()->after('instansi_id');
            $table->unsignedBigInteger('lender_id')->nullable()->after('company_id');
            $table->unsignedBigInteger('referral_id')->nullable()->after('lender_id');
            $table->index('lender_id', 'admins_lender_id_index');
            $table->index('referral_id', 'admins_referral_id_index');
        });

        $internalId = DB::table('instansis')->where('code', 'internal')->value('id');
        $adminRoleId = DB::table('roles')
            ->where('instansi_id', $internalId)
            ->where('code', 'admin')
            ->value('id');
        $superAdminRoleId = DB::table('roles')
            ->where('instansi_id', $internalId)
            ->where('code', 'super_admin')
            ->value('id');

        DB::table('admins')->where('type', 'master')->update([
            'instansi_id' => $internalId,
            'role_id' => $superAdminRoleId,
        ]);

        DB::table('admins')->where(function ($query): void {
            $query->where('type', 'admin')->orWhereNull('type');
        })->update([
            'instansi_id' => $internalId,
            'role_id' => $adminRoleId,
        ]);

        DB::statement('ALTER TABLE `admins` MODIFY `company_id` BIGINT UNSIGNED NULL');

        Schema::table('admins', function (Blueprint $table): void {
            $table->foreign('instansi_id', 'admins_instansi_id_foreign')
                ->references('id')->on('instansis')->restrictOnDelete();
            $table->foreign('role_id', 'admins_role_id_foreign')
                ->references('id')->on('roles')->restrictOnDelete();
            $table->foreign('lender_id', 'admins_lender_id_foreign')
                ->references('id')->on('lenders')->nullOnDelete();
            $table->foreign('company_id', 'admins_company_id_foreign')
                ->references('id')->on('list_perusahaans')->nullOnDelete();
            $table->foreign('referral_id', 'admins_referral_id_foreign')
                ->references('id')->on('referrals')->nullOnDelete();
        });

        Schema::table('list_perusahaans', function (Blueprint $table): void {
            $table->unsignedBigInteger('admin_id')->nullable()->after('id_lender');
            $table->index('admin_id', 'list_perusahaans_admin_id_index');
            $table->foreign('admin_id', 'list_perusahaans_admin_id_foreign')
                ->references('id')->on('admins')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('admins', function (Blueprint $table): void {
            $table->dropForeign('admins_referral_id_foreign');
            $table->dropForeign('admins_company_id_foreign');
            $table->dropForeign('admins_lender_id_foreign');
            $table->dropForeign('admins_role_id_foreign');
            $table->dropForeign('admins_instansi_id_foreign');
            $table->dropIndex('admins_referral_id_index');
            $table->dropIndex('admins_lender_id_index');
            $table->dropColumn(['referral_id', 'lender_id', 'role_id', 'instansi_id']);
        });

        Schema::table('list_perusahaans', function (Blueprint $table): void {
            $table->dropForeign('list_perusahaans_admin_id_foreign');
            $table->dropIndex('list_perusahaans_admin_id_index');
            $table->dropColumn('admin_id');
        });

        Schema::dropIfExists('roles');
        Schema::dropIfExists('instansis');
    }
};
