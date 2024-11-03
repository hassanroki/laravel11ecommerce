<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $guarded = [];

    // Category Table Relation
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    // Brand Table Relation
    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }
}
