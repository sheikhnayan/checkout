<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Transaction;
use App\Models\Package;
use App\Http\Controllers\TransactionController;

class FixAndResendMismatchedTransactions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'transactions:fix-and-resend 
                            {--transaction_id= : Specific transaction ID or database ID to target} 
                            {--resend-email : Automatically resend corrected confirmation emails} 
                            {--dry-run : Only display mismatched transactions without modifying database}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Find and fix transactions where website_id does not match the true package owner club, and optionally resend confirmation emails.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $targetId = $this->option('transaction_id');
        $resend = $this->option('resend-email');
        $dryRun = $this->option('dry-run');

        $query = Transaction::orderByDesc('id');
        if ($targetId) {
            $query->where(function ($q) use ($targetId) {
                $q->where('id', $targetId)->orWhere('transaction_id', $targetId);
            });
        }

        $transactions = $query->get();
        $controller = new TransactionController();
        $fixedCount = 0;
        $emailCount = 0;

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

            if ($p && !empty($p->website_id) && (int) $p->website_id !== (int) $t->website_id) {
                $oldWeb = $t->website?->name ?? "ID #{$t->website_id}";
                $newWeb = $p->website?->name ?? "ID #{$p->website_id}";

                $this->info("Found mismatch for Transaction #{$t->id} ({$t->transaction_id}):");
                $this->line("  Package: {$p->name} (Package Website ID: {$p->website_id} - {$newWeb})");
                $this->line("  Current Website: {$oldWeb} -> Target Website: {$newWeb}");

                if (!$dryRun) {
                    $t->website_id = (int) $p->website_id;
                    $t->save();
                    $fixedCount++;

                    if ($resend) {
                        try {
                            $sent = $controller->sendConfirmationEmailForTransaction($t);
                            $emailCount += $sent;
                            $this->info("  ✓ Resent confirmation email ({$sent} email(s) sent).");
                        } catch (\Throwable $e) {
                            $this->error("  ✗ Failed to send email: " . $e->getMessage());
                        }
                    }
                }
            }
        }

        if ($dryRun) {
            $this->warn("Dry run complete. No database changes were executed.");
        } else {
            $this->info("Process complete! Fixed {$fixedCount} transaction(s) and resent {$emailCount} confirmation email(s).");
        }

        return 0;
    }
}
