<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CustomInvoice extends Model
{
    protected $fillable = [
        'user_id',
        'website_id',
        'client_name',
        'client_email',
        'notes',
        'subtotal',
        'gratuity',
        'gratuity_name',
        'refundable',
        'refundable_name',
        'sales_tax',
        'sales_tax_name',
        'tax',
        'service_charge',
        'service_charge_name',
        'total',
        'status',
        'payment_token',
        'sent_at',
        'paid_at',
        'payment_transaction_id',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'paid_at' => 'datetime',
        'subtotal' => 'decimal:2',
        'gratuity' => 'decimal:2',
        'refundable' => 'decimal:2',
        'sales_tax' => 'decimal:2',
        'tax' => 'decimal:2',
        'service_charge' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function website()
    {
        return $this->belongsTo(Website::class);
    }

    public function items()
    {
        return $this->hasMany(CustomInvoiceItem::class);
    }

    public static function generatePaymentToken()
    {
        $token = Str::random(32);
        while (self::where('payment_token', $token)->exists()) {
            $token = Str::random(32);
        }
        return $token;
    }

    public function calculateTotals()
    {
        $subtotal = $this->items->sum(function ($item) {
            return $item->price * $item->quantity;
        });

        $website = $this->website;
        
        // Calculate sales tax first
        $salesTax = 0;
        $salesTaxName = null;
        if ($website && $website->sales_tax_fee) {
            $salesTax = round($subtotal * ($website->sales_tax_fee / 100), 2);
            $salesTaxName = $website->sales_tax_name ?: 'Sales Tax';
        }

        // Calculate service charge
        $serviceCharge = 0;
        $serviceChargeName = null;
        if ($website && $website->service_charge_fee) {
            $serviceCharge = round($subtotal * ($website->service_charge_fee / 100), 2);
            $serviceChargeName = $website->service_charge_name ?: 'Service Charge';
        }

        // Calculate gratuity on (subtotal + sales_tax + service_charge) - matches main system
        $gratuity = 0;
        $gratuityName = null;
        if ($website && $website->gratuity_fee) {
            $baseForGratuity = $subtotal + $salesTax + $serviceCharge;
            $gratuity = round($baseForGratuity * ($website->gratuity_fee / 100), 2);
            $gratuityName = $website->gratuity_name ?: 'Gratuity Fee';
        }

        // Calculate refundable fee (stored for reference but NOT added to total)
        // This represents a non-refundable deposit in the main system
        $refundable = 0;
        $refundableName = null;
        if ($website && $website->refundable_fee) {
            $refundable = round($subtotal * ($website->refundable_fee / 100), 2);
            $refundableName = $website->refundable_name ?: 'Non-Refundable Deposit';
        }

        // Calculate total without refundable (matches main system behavior)
        $total = $subtotal + $salesTax + $serviceCharge + $gratuity;

        $this->subtotal = $subtotal;
        $this->gratuity = $gratuity;
        $this->gratuity_name = $gratuityName;
        $this->refundable = $refundable;
        $this->refundable_name = $refundableName;
        $this->sales_tax = $salesTax;
        $this->sales_tax_name = $salesTaxName;
        $this->tax = $salesTax; // Backward compatibility
        $this->service_charge = $serviceCharge;
        $this->service_charge_name = $serviceChargeName;
        $this->total = $total;

        return $this;
    }

    public function getPaymentUrl()
    {
        return url('/custom-invoice/' . $this->payment_token . '/pay');
    }
}
