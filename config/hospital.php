<?php

return [
    'default_registration_fee' => 200,

    'laboratory_tests' => [
        'cbc' => ['name' => 'CBC', 'fee' => 400],
        'blood_sugar' => ['name' => 'Blood Sugar', 'fee' => 300],
        'urine' => ['name' => 'Urine Test', 'fee' => 250],
        'thyroid' => ['name' => 'Thyroid', 'fee' => 500],
        'cholesterol' => ['name' => 'Cholesterol', 'fee' => 350],
    ],

    'radiology_scans' => [
        'xray' => ['name' => 'X-Ray', 'fee' => 800],
        'mri' => ['name' => 'MRI', 'fee' => 5000],
        'ct_scan' => ['name' => 'CT Scan', 'fee' => 3500],
        'ultrasound' => ['name' => 'Ultrasound', 'fee' => 1200],
    ],
];
