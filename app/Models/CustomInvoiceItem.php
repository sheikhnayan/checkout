<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomInvoiceItem extends Model
{
    protected $fillable = [
        'custom_invoice_id',
        'name',
        'guests',
        'description',
        'quantity',
        'price',
    ];

    protected $casts = [
        'guests' => 'integer',
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
