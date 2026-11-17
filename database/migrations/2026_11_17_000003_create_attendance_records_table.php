<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance_records', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->bigIncrements('id');
            $table->bigInteger('user_id')->unsigned();
            $table->bigInteger('employee_id')->unsigned()->nullable();
            $table->bigInteger('company_id')->unsigned()->nullable();
            $table->date('date_key')->nullable();
            $table->string('employee_name', 255)->nullable();
            $table->string('employee_nip', 255)->nullable();
            $table->string('scope_key', 255)->nullable();
            $table->string('company', 255)->nullable();
            $table->string('department', 255)->nullable();
            $table->string('position', 255)->nullable();
            $table->enum('work_mode', ['WFO', 'WFA'])->nullable();
            $table->timestamp('check_in_at')->nullable();
            $table->timestamp('check_out_at')->nullable();
            $table->string('check_in_photo', 255)->nullable();
            $table->string('check_out_photo', 255)->nullable();
            $table->timestamp('overtime_requested_at')->nullable();
            $table->date('overtime_date')->nullable();
            $table->time('overtime_start_time')->nullable();
            $table->decimal('overtime_hours', 5, 2)->nullable();
            $table->decimal('overtime_actual_hours', 5, 2)->nullable();
            $table->text('overtime_reason')->nullable();
            $table->timestamp('end_overtime_at')->nullable();
            $table->timestamp('wfa_requested_at')->nullable();
            $table->date('wfa_date')->nullable();
            $table->text('wfa_reason')->nullable();
            $table->timestamp('leave_at')->nullable();
            $table->enum('leave_approval_status', ['pending', 'approved', 'rejected'])->nullable();
            $table->enum('leave_type', ['annual', 'special', 'permission', 'sick'])->nullable();
            $table->string('special_leave_type', 255)->nullable();
            $table->date('leave_date')->nullable();
            $table->date('leave_end_date')->nullable();
            $table->text('leave_reason')->nullable();
            $table->string('leave_attachment', 255)->nullable();
            $table->decimal('last_distance', 10, 2)->nullable();
            $table->decimal('last_accuracy', 10, 2)->nullable();
            $table->decimal('last_latitude', 11, 8)->nullable();
            $table->decimal('last_longitude', 11, 8)->nullable();
            $table->text('last_address')->nullable();
            $table->enum('approval_status', ['pending', 'approved', 'rejected'])->nullable();
            $table->text('approval_note')->nullable();
            $table->bigInteger('approved_by')->unsigned()->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('deleted_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_records');
    }
};
