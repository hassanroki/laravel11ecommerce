<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $guarded = [];

    // User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Order Items
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    // Transaction
    public function transaction()
    {
        return $this->hasOne(transaction::class);
    }
}
