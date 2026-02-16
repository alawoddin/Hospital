<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bills', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_no')->unique();
            $table->unsignedBigInteger('patient_id')->nullable();
            $table->date('bill_date');
            $table->decimal('discount', 12, 2)->nullable();
            $table->decimal('total_amount', 12, 2);
            $table->decimal('due_amount', 12, 2)->default(0);
            $table->enum('status', ['pending','partially_paid','paid','canceled'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bills');
    }
};
