<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('doctor_consultations', function (Blueprint $table) {
            $table->foreignId('fee_type_id')->nullable()->after('appointment_id')->constrained('fee_types')->nullOnDelete();
        });

        Schema::table('lab_requests', function (Blueprint $table) {
            $table->foreignId('fee_type_id')->nullable()->after('appointment_id')->constrained('fee_types')->nullOnDelete();
            $table->decimal('fee_amount', 10, 2)->default(0)->after('fee_type_id');
        });

        Schema::table('patients', function (Blueprint $table) {
            $table->foreignId('registration_fee_type_id')->nullable()->after('registration_fee')->constrained('fee_types')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->dropConstrainedForeignId('registration_fee_type_id');
        });

        Schema::table('lab_requests', function (Blueprint $table) {
            $table->dropConstrainedForeignId('fee_type_id');
            $table->dropColumn('fee_amount');
        });

        Schema::table('doctor_consultations', function (Blueprint $table) {
            $table->dropConstrainedForeignId('fee_type_id');
        });
    }
};
