<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE users MODIFY role ENUM('admin','doctor','recieption','finance','pharmacy','laboratory','radiology','user') NOT NULL DEFAULT 'user'");

        Schema::table('bill_items', function (Blueprint $table) {
            if (! Schema::hasColumn('bill_items', 'category')) {
                $table->string('category')->default('other')->after('bill_id');
            }
            if (! Schema::hasColumn('bill_items', 'reference_type')) {
                $table->string('reference_type')->nullable()->after('total_price');
            }
            if (! Schema::hasColumn('bill_items', 'reference_id')) {
                $table->unsignedBigInteger('reference_id')->nullable()->after('reference_type');
            }
        });

        Schema::table('bills', function (Blueprint $table) {
            if (! Schema::hasColumn('bills', 'is_master')) {
                $table->boolean('is_master')->default(true)->after('appointment_id');
            }
        });

        Schema::table('lab_requests', function (Blueprint $table) {
            if (! Schema::hasColumn('lab_requests', 'report_file')) {
                $table->string('report_file')->nullable()->after('result');
            }
            if (! Schema::hasColumn('lab_requests', 'completed_at')) {
                $table->timestamp('completed_at')->nullable()->after('report_file');
            }
            if (! Schema::hasColumn('lab_requests', 'completed_by')) {
                $table->foreignId('completed_by')->nullable()->constrained('users')->nullOnDelete()->after('completed_at');
            }
            if (! Schema::hasColumn('lab_requests', 'bill_item_id')) {
                $table->unsignedBigInteger('bill_item_id')->nullable()->after('fee_amount');
            }
        });

        if (! Schema::hasTable('radiology_requests')) {
            Schema::create('radiology_requests', function (Blueprint $table) {
                $table->id();
                $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
                $table->foreignId('doctor_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('appointment_id')->nullable()->constrained()->nullOnDelete();
                $table->string('scan_type');
                $table->text('instructions')->nullable();
                $table->decimal('fee_amount', 10, 2)->default(0);
                $table->enum('status', ['pending', 'in_progress', 'completed', 'cancelled'])->default('pending');
                $table->text('result')->nullable();
                $table->string('report_file')->nullable();
                $table->timestamp('completed_at')->nullable();
                $table->foreignId('completed_by')->nullable()->constrained('users')->nullOnDelete();
                $table->unsignedBigInteger('bill_item_id')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('expenses')) {
            Schema::create('expenses', function (Blueprint $table) {
                $table->id();
                $table->string('category');
                $table->string('title');
                $table->text('description')->nullable();
                $table->decimal('amount', 12, 2);
                $table->date('expense_date');
                $table->enum('status', ['pending', 'approved', 'paid'])->default('pending');
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
                $table->date('payment_date')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('salaries')) {
            Schema::create('salaries', function (Blueprint $table) {
                $table->id();
                $table->foreignId('employee_id')->constrained('users')->cascadeOnDelete();
                $table->string('month');
                $table->decimal('amount', 12, 2);
                $table->enum('status', ['pending', 'approved', 'paid'])->default('pending');
                $table->date('payment_date')->nullable();
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('salaries');
        Schema::dropIfExists('expenses');
        Schema::dropIfExists('radiology_requests');

        Schema::table('lab_requests', function (Blueprint $table) {
            $table->dropConstrainedForeignId('completed_by');
            $table->dropColumn(['report_file', 'completed_at', 'bill_item_id']);
        });

        Schema::table('bills', function (Blueprint $table) {
            $table->dropColumn('is_master');
        });

        Schema::table('bill_items', function (Blueprint $table) {
            $table->dropColumn(['category', 'reference_type', 'reference_id']);
        });

        DB::statement("ALTER TABLE users MODIFY role ENUM('admin','doctor','recieption','finance','pharmacy','user') NOT NULL DEFAULT 'user'");
    }
};
