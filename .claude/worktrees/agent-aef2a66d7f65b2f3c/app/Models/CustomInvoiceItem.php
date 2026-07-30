<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomInvoiceItem extends Model
{
    protected $fillable = [
        'custom_invoice_id',
        'name',
        'description',
        'quantity',
        'price',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    public function customInvoice()
    {
        return $this->belongsTo(CustomInvoice::class);
    }

    public function getLineTotal()
    {
        return $this->price * $this->quantity;
    }
}
