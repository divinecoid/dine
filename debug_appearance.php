<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    echo "Checking User 2...\n";
    $user = \App\Models\User::find(2);
    if (!$user) {
        die("User 2 not found\n");
    }
    echo "Current appearance: " . ($user->appearance ?? 'null') . "\n";

    echo "Attempting update to 'light'...\n";
    $user->update(['appearance' => 'light']);

    echo "Update successful!\n";
    echo "New appearance: " . $user->refresh()->appearance . "\n";
} catch (\Throwable $e) {
    echo "EXCEPTION CAUGHT:\n";
    echo $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
}
