<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'slug',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function serviceProviders()
    {
        return $this->hasMany(ServiceProvider::class);
    }

}
