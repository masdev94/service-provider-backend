<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;

class ServiceProvider extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'short_description',
        'description',
        'logo',
        'category_id',
    ];
    protected $appends = ['logo_url'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }
    public function getLogoUrlAttribute()
    {
        if (!$this->logo) {
            return null;
        }

        return config('app.url') . $this->logo;
    }

}
