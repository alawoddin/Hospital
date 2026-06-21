<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('medicines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('medicine_category_id')->nullable()->constrained('medicine_categories')->nullOnDelete();
            $table->string('name');
            $table->string('generic_name')->nullable();
            $table->text('description')->nullable();
            $table->decimal('unit_price', 10, 2)->default(0);
            $table->string('unit')->default('tablet');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medicines');
    }
};
