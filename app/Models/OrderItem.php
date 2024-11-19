<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $guarded = [];

    // Product
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Order
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
