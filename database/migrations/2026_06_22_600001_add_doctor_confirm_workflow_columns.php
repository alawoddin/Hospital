<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lab_requests', function (Blueprint $table) {
            if (! Schema::hasColumn('lab_requests', 'doctor_confirmed_at')) {
                $table->timestamp('doctor_confirmed_at')->nullable()->after('completed_by');
            }
        });

        Schema::table('prescriptions', function (Blueprint $table) {
            if (! Schema::hasColumn('prescriptions', 'doctor_confirmed_at')) {
                $table->timestamp('doctor_confirmed_at')->nullable()->after('status');
            }
            if (! Schema::hasColumn('prescriptions', 'dispensed_at')) {
                $table->timestamp('dispensed_at')->nullable()->after('doctor_confirmed_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('lab_requests', function (Blueprint $table) {
            $table->dropColumn('doctor_confirmed_at');
        });

        Schema::table('prescriptions', function (Blueprint $table) {
            $table->dropColumn(['doctor_confirmed_at', 'dispensed_at']);
        });
    }
};
