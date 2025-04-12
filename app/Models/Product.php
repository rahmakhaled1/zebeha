<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'supcategory_id',
        'name',
        'description',
        'price',
        'stock',
        'discount_percentage',
    ];

    public function supcategory()
    {
        return $this->belongsTo(SupCategory::class);
    }

    public function images()
    {
        return $this->morphMany(Images::class, 'imageable');
    }

    public function offers()
    {
        return $this->hasMany(Offer::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }



}
