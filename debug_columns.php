<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Columns in users table:\n";
$columns = \Illuminate\Support\Facades\Schema::getColumnListing('users');
foreach ($columns as $col) {
    echo "- " . $col . "\n";
}
