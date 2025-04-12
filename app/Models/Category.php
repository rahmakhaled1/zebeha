<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $table = 'categories';
    protected $fillable = [
        'title',
        'description',
        'image',
    ];

    public function subcategories()
    {
        return $this->hasMany(SupCategory::class, 'category_id');
    }
}
