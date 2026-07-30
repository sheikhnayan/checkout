<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Website;

echo "Generating slugs for existing websites...\n\n";

$websites = Website::whereNull('slug')->get();

if ($websites->count() === 0) {
    echo "No websites found without slugs. All websites already have slugs!\n";
} else {
    foreach ($websites as $website) {
        $slug = Website::generateSlug($website->name, $website->id);
        $website->slug = $slug;
        $website->save();
        
        echo "✓ Generated slug for '{$website->name}': {$slug}\n";
    }
    
    echo "\n✅ Successfully generated " . $websites->count() . " slugs!\n";
}

echo "\nAll websites now have slugs:\n";
echo "-----------------------------------\n";

$allWebsites = Website::all();
foreach ($allWebsites as $website) {
    echo "• {$website->name} → {$website->slug}\n";
}
