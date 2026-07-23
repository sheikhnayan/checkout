<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Test search filter options endpoint
$user = \App\Models\User::where('role', 'admin')->first();
if (!$user) {
    echo "No admin user found\n";
    exit(1);
}

auth()->login($user);

// Create a request
$request = \Illuminate\Http\Request::create(
    route('admin.transaction.search-filter-options'),
    'POST',
    [],
    [],
    [],
    ['HTTP_X_REQUESTED_WITH' => 'XMLHttpRequest']
);

$controller = app(\App\Http\Controllers\TransactionController::class);
try {
    $response = $controller->getSearchFilterOptions($request);
    echo "Status: " . $response->status() . "\n";
    echo "Content:\n";
    echo $response->getContent();
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}
