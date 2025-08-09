<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = [
        'supplier_id',
        'service_api_id',
        'name',
        'type',
        'category',
        'rate',
        'min',
        'max',
        'refill',
        'cancel',
        'status'
    ];
}
