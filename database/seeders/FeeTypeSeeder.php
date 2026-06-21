<?php

namespace Database\Seeders;

use App\Models\FeeType;
use Illuminate\Database\Seeder;

class FeeTypeSeeder extends Seeder
{
    public function run(): void
    {
        $fees = [
            ['name' => 'Patient Registration', 'category' => 'registration', 'amount' => 50, 'description' => 'New patient registration fee'],
            ['name' => 'OPD Consultation', 'category' => 'consultation', 'amount' => 200, 'description' => 'General OPD doctor visit'],
            ['name' => 'Neurology Consultation', 'category' => 'consultation', 'amount' => 300, 'description' => 'Neurology specialist visit'],
            ['name' => 'Cardiology Consultation', 'category' => 'consultation', 'amount' => 350, 'description' => 'Cardiology specialist visit'],
            ['name' => 'CBC Blood Test', 'category' => 'laboratory', 'amount' => 150, 'description' => 'Complete blood count'],
            ['name' => 'Urine Analysis', 'category' => 'laboratory', 'amount' => 80, 'description' => 'Urine routine test'],
            ['name' => 'X-Ray', 'category' => 'laboratory', 'amount' => 500, 'description' => 'X-Ray imaging'],
            ['name' => 'MRI Scan', 'category' => 'laboratory', 'amount' => 2500, 'description' => 'MRI imaging'],
        ];

        foreach ($fees as $fee) {
            FeeType::updateOrCreate(
                ['name' => $fee['name'], 'category' => $fee['category']],
                array_merge($fee, ['is_active' => true])
            );
        }
    }
}
