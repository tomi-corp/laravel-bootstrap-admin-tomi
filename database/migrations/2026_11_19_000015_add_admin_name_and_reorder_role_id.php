<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('admins', 'name')) {
            DB::statement('ALTER TABLE `admins` ADD COLUMN `name` varchar(255) NULL AFTER `id`');
        }

        DB::statement("UPDATE `admins` SET `name` = `username` WHERE `name` IS NULL OR `name` = ''");

        if (Schema::hasColumn('admins', 'role_id')) {
            DB::statement('ALTER TABLE `admins` MODIFY COLUMN `role_id` bigint unsigned NULL AFTER `type`');
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('admins', 'role_id')) {
            DB::statement('ALTER TABLE `admins` MODIFY COLUMN `role_id` bigint unsigned NULL AFTER `instansi_id`');
        }

        if (Schema::hasColumn('admins', 'name')) {
            DB::statement('ALTER TABLE `admins` DROP COLUMN `name`');
        }
    }
};
