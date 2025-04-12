<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;

    protected $table = 'coupons';

    protected $fillable = [
        'code',
        'discount_percentage',
        'expiry_date',
        'status',
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'coupon_product');
    }

}
