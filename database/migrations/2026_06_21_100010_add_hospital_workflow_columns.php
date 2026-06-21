<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->foreignId('doctor_id')->nullable()->after('doctor')->constrained('users')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->after('doctor_id')->constrained('users')->nullOnDelete();
            $table->foreignId('department_id')->nullable()->after('user_id')->constrained('departments')->nullOnDelete();
        });

        Schema::table('appointments', function (Blueprint $table) {
            $table->timestamp('checked_in_at')->nullable()->after('status');
        });

        Schema::table('prescriptions', function (Blueprint $table) {
            $table->foreignId('appointment_id')->nullable()->after('pharmacy_id')->constrained('appointments')->nullOnDelete();
            $table->enum('status', ['pending', 'partially_dispensed', 'dispensed'])->default('pending')->after('appointment_id');
            $table->text('notes')->nullable()->after('status');
        });

        Schema::table('prescription_items', function (Blueprint $table) {
            $table->foreignId('medicine_id')->nullable()->after('prescription_id')->constrained('medicines')->nullOnDelete();
            $table->string('dosage')->nullable()->after('desc');
            $table->string('frequency')->nullable()->after('dosage');
            $table->integer('quantity')->default(1)->after('frequency');
            $table->boolean('dispensed')->default(false)->after('quantity');
        });

        Schema::table('bills', function (Blueprint $table) {
            $table->foreignId('appointment_id')->nullable()->after('patient_id')->constrained('appointments')->nullOnDelete();
            $table->text('notes')->nullable()->after('status');
            $table->foreign('patient_id')->references('id')->on('patients')->nullOnDelete();
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->string('payment_method')->default('cash')->after('payment_date');
            $table->string('reference_no')->nullable()->after('payment_method');
            $table->foreignId('received_by')->nullable()->after('reference_no')->constrained('users')->nullOnDelete();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('department_id')->nullable()->after('role')->constrained('departments')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('department_id');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropConstrainedForeignId('received_by');
            $table->dropColumn(['payment_method', 'reference_no']);
        });

        Schema::table('bills', function (Blueprint $table) {
            $table->dropForeign(['patient_id']);
            $table->dropConstrainedForeignId('appointment_id');
            $table->dropColumn('notes');
        });

        Schema::table('prescription_items', function (Blueprint $table) {
            $table->dropConstrainedForeignId('medicine_id');
            $table->dropColumn(['dosage', 'frequency', 'quantity', 'dispensed']);
        });

        Schema::table('prescriptions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('appointment_id');
            $table->dropColumn(['status', 'notes']);
        });

        Schema::table('appointments', function (Blueprint $table) {
            $table->dropColumn('checked_in_at');
        });

        Schema::table('patients', function (Blueprint $table) {
            $table->dropConstrainedForeignId('doctor_id');
            $table->dropConstrainedForeignId('user_id');
            $table->dropConstrainedForeignId('department_id');
        });
    }
};
