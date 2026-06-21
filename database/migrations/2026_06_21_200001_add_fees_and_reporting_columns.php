<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->decimal('consultation_fee', 10, 2)->default(0)->after('address');
        });

        Schema::table('patients', function (Blueprint $table) {
            $table->decimal('registration_fee', 10, 2)->default(0)->after('national_id');
        });

        Schema::table('appointments', function (Blueprint $table) {
            $table->decimal('consultation_fee', 10, 2)->nullable()->after('checked_in_at');
            $table->foreignId('checked_in_by')->nullable()->after('consultation_fee')->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropConstrainedForeignId('checked_in_by');
            $table->dropColumn('consultation_fee');
        });

        Schema::table('patients', function (Blueprint $table) {
            $table->dropColumn('registration_fee');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('consultation_fee');
        });
    }
};
