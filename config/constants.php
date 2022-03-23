<?php
return [
    'per_page' => 20,
    'allow_ip' => [
        'allow' => env('ENABLE_ACCESS_IP', true),
        'list'  => [
            1 => '172.19.0.2',
            2 => '192.168.24.22',
            3 => '192.168.253.22'
        ],
    ],
    'ADMIN_ROLE' => 1,
    'MANAGER_ROLE' => 2,
];
