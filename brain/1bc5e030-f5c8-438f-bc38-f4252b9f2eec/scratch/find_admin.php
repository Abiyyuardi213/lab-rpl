<?php

use App\Models\User;

require __DIR__ . '/../../../vendor/autoload.php';
$app = require_once __DIR__ . '/../../../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$adminUser = User::whereHas('role', function($q) {
    $q->where('name', 'Admin')->orWhere('name', 'Super Admin');
})->first();

if ($adminUser) {
    echo "Admin Username: {$adminUser->username}\n";
} else {
    echo "No admin user found!\n";
}
