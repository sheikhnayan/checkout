<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Transaction;
use App\Models\Package;
use App\Models\Website;

$transactions = Transaction::orderByDesc('id')->get();
$mismatches = [];

foreach ($transactions as $t) {
    $cart = is_array($t->cart_items) ? $t->cart_items : (json_decode($t->cart_items, true) ?: []);
    $pkgId = $t->package_id ?: ($cart[0]['package_id'] ?? null);
    $pkgName = $cart[0]['package_name'] ?? ($cart[0]['name'] ?? null);
    
    $p = null;
    if ($pkgId) {
        $p = Package::find($pkgId);
    } elseif ($pkgName) {
        $p = Package::where('name', $pkgName)->first();
    }
    
    if ($p && (int)$p->website_id !== (int)$t->website_id) {
        $currentWeb = Website::find($t->website_id);
        $correctWeb = Website::find($p->website_id);
        $mismatches[] = [
            'id' => $t->id,
            'transaction_id' => $t->transaction_id,
            'current_website_id' => $t->website_id,
            'current_website_name' => $currentWeb?->name,
            'correct_website_id' => $p->website_id,
            'correct_website_name' => $correctWeb?->name,
            'package_id' => $p->id,
            'package_name' => $p->name,
            'created_at' => (string) $t->created_at,
        ];

        // Fix the transaction's website_id to match the package's true website_id!
        $t->website_id = $p->website_id;
        $t->save();
    }
}

echo "Fixed " . count($mismatches) . " mismatched transactions:\n";
print_r($mismatches);
