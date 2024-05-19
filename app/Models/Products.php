<?php

namespace App\Models;
use App\Models\Categories;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Products extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'price',
        'category_id',
        'expired_at',
        'modified_by',
        'image',
        'description'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'modified_by');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            if (auth()->check()) {
                $product->modified_by = auth()->user()->email;
            }
        });
        static::updating(function ($product) {
            if (auth()->check()) {
                $product->modified_by = auth()->user()->email;
            }
        });
    }
    public function category()
    { 
        return $this->belongsTo(Categories::class);
    }
}
