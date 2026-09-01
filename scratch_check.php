<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\CustomInvoice;

$inv = CustomInvoice::find(9);
if ($inv) {
    $inv->status = 'sent';
    $inv->paid_at = null;
    $inv->payment_transaction_id = null;
    $inv->save();
    echo "Reset Invoice #9 status back to 'sent' successfully.\n";
} else {
    echo "Invoice #9 not found.\n";
}
