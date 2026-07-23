<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->index();
            $table->unsignedBigInteger('employee_id')->nullable()->index();
            $table->unsignedBigInteger('company_id')->nullable()->index();
            $table->date('date_key')->nullable()->index();
            $table->string('employee_name')->nullable();
            $table->string('employee_nip')->nullable()->index();
            $table->string('scope_key')->nullable();
            $table->string('company')->nullable();
            $table->string('department')->nullable();
            $table->string('position')->nullable();
            $table->enum('work_mode', ['WFO', 'WFA'])->nullable();
            $table->timestamp('check_in_at')->nullable();
            $table->timestamp('check_out_at')->nullable();
            $table->string('check_in_photo')->nullable();
            $table->string('check_out_photo')->nullable();
            $table->timestamp('overtime_requested_at')->nullable();
            $table->date('overtime_date')->nullable()->index();
            $table->time('overtime_start_time')->nullable();
            $table->decimal('overtime_hours', 5, 2)->nullable();
            $table->decimal('overtime_actual_hours', 5, 2)->nullable();
            $table->text('overtime_reason')->nullable();
            $table->timestamp('end_overtime_at')->nullable();
            $table->timestamp('wfa_requested_at')->nullable();
            $table->date('wfa_date')->nullable()->index();
            $table->text('wfa_reason')->nullable();
            $table->timestamp('leave_at')->nullable();
            $table->enum('leave_approval_status', ['pending', 'approved', 'rejected'])->nullable()->index();
            $table->enum('leave_type', ['annual', 'special', 'permission', 'sick'])->nullable()->index();
            $table->string('special_leave_type')->nullable();
            $table->date('leave_date')->nullable()->index();
            $table->date('leave_end_date')->nullable()->index();
            $table->text('leave_reason')->nullable();
            $table->string('leave_attachment')->nullable();
            $table->decimal('last_distance', 10, 2)->nullable();
            $table->decimal('last_accuracy', 10, 2)->nullable();
            $table->decimal('last_latitude', 11, 8)->nullable();
            $table->decimal('last_longitude', 11, 8)->nullable();
            $table->text('last_address')->nullable();
            $table->enum('approval_status', ['pending', 'approved', 'rejected'])->nullable()->index();
            $table->text('approval_note')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'date_key']);
            $table->index(['user_id', 'overtime_date']);
            $table->index(['user_id', 'wfa_date']);
            $table->index(['user_id', 'leave_date', 'leave_end_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_records');
    }
};
