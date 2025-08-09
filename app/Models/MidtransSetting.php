<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MidtransSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'server_key',
        'client_key',
        'mode'
    ];
}
