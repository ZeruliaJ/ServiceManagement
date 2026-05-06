<?php

use Illuminate\Support\Facades\Hash;
use App\Models\User;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';

// Manually boot the app
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Create a user
User::create([
    'name' => 'Normal User',
    'email' => 'zerulia.jackson@cargen.co.tz',
    'password' => Hash::make('Password123!'), // hashed correctly
]);

echo "User created!\n";