<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceProvider extends Model
{

    protected $fillable = [
        'name',
        'slug',
        'short_description',
        'description',
        'logo',
        'category_id',
    ];


    public function category()
    {
        return $this->belongsTo(Category::class);
    }

}
