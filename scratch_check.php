<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Website;

$websites = Website::all();
echo "=== WEBSITES OPERATING HOURS ===\n";
foreach ($websites as $w) {
    echo "Website ID {$w->id}: {$w->name}\n";
    echo "  operating_start_time: " . ($w->operating_start_time ?: 'NULL') . "\n";
    echo "  operating_end_time: " . ($w->operating_end_time ?: 'NULL') . "\n";
    $daily = $w->getDailyOperatingHoursMap();
    echo "  Daily hours: " . json_encode($daily) . "\n\n";
}
