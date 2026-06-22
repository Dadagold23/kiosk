<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'type',
        'name',
        'slug',
        'description',
        'icon',
        'status',
    ];

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function serviceRequests()
    {
        return $this->hasMany(ServiceRequest::class);
    }

    public function consultancyRequests()
    {
        return $this->hasMany(ConsultancyRequest::class);
    }
}
