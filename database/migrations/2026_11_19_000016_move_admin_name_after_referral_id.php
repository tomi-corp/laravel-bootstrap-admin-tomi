<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('admins', 'name')) {
            DB::statement('ALTER TABLE `admins` MODIFY COLUMN `name` varchar(255) NULL AFTER `referral_id`');
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('admins', 'name')) {
            DB::statement('ALTER TABLE `admins` MODIFY COLUMN `name` varchar(255) NULL AFTER `id`');
        }
    }
};
