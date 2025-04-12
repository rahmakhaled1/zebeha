<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{
    use HasFactory;
    protected $table = 'offers';
    protected $fillable = [
        'supcategory_id',
        'product_id',
        'title',
        'description',
        'price',
        'gift',
        'start_date',
        'end_date',
        'status',
    ];

    public function superCategory()
    {
        return $this->belongsTo(SupCategory::class);
    }


    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function images()
    {
        return $this->morphMany(Images::class, 'imageable');
    }
}
