<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
        'stock',
        'description',
        'status',
        'category_id',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'stock' => 'integer',
        'min_stock' => 'integer',
    ];

    /**
     * Relationship: Product belongs to Category.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Relationship: Product has many images.
     */
    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    /**
     * Stock status accessor: returns status text and CSS class.
     */
    public function getStockStatusAttribute()
    {
        if ($this->stock <= 0) {
            return [
                'status' => 'out-of-stock',
                'text' => 'Out of Stock',
                'class' => 'out-of-stock',
            ];
        } elseif ($this->stock <= $this->min_stock) {
            return [
                'status' => 'low-stock',
                'text' => 'Low Stock',
                'class' => 'low-stock',
            ];
        } else {
            return [
                'status' => 'in-stock',
                'text' => 'In Stock',
                'class' => 'in-stock',
            ];
        }
    }

    /**
     * Formatted price accessor, e.g. "$12.34"
     */
    public function getFormattedPriceAttribute()
    {
        return '$' . number_format($this->price, 2);
    }

    /**
     * Brand scope (if applicable)
     */
    public function scopeByBrand($query, $brand)
    {
        return $query->when($brand, function ($query, $brand) {
            return $query->where('brand', $brand);
        });
    }

    /**
     * Active products scope
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
