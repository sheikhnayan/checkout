<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\CustomInvoice;

$invoices = CustomInvoice::all();
foreach ($invoices as $inv) {
    if ($inv->status === 'paid' && !\App\Models\Transaction::where('custom_invoice_id', $inv->id)->exists()) {
        $inv->status = 'sent';
        $inv->paid_at = null;
        $inv->payment_transaction_id = null;
        $inv->save();
        echo "Reset Invoice #{$inv->id} status back to 'sent'.\n";
    }
}
