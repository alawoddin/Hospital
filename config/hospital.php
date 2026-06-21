<?php

return [
    /*
    | Laboratory tests available at reception check-in (same patient visit).
    | Admin can edit fees here until a settings page is added.
    */
    'laboratory_tests' => [
        'cbc' => ['name' => 'CBC Blood Test', 'fee' => 150],
        'urine' => ['name' => 'Urine Analysis', 'fee' => 80],
        'xray' => ['name' => 'X-Ray', 'fee' => 500],
        'mri' => ['name' => 'MRI Scan', 'fee' => 2500],
    ],
];
