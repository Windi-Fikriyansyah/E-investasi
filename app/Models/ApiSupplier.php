<?php

// app/Models/ApiSupplier.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApiSupplier extends Model
{
    protected $fillable = ['name', 'api_key', 'api_url', 'status', 'api_format', 'api_services_action'];
}
