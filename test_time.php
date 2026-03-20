<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Config Timezone: " . config('app.timezone') . "\n";
echo "Now (PHP): " . date('Y-m-d H:i:s') . "\n";
echo "Now (Carbon): " . \Carbon\Carbon::now()->toDateTimeString() . "\n";
?>
