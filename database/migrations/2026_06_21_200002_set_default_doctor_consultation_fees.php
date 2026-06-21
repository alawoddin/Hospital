<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        User::where('role', 'doctor')
            ->where(function ($query) {
                $query->whereNull('consultation_fee')
                    ->orWhere('consultation_fee', '<=', 0);
            })
            ->update(['consultation_fee' => 250]);
    }

    public function down(): void
    {
        // No rollback needed
    }
};
