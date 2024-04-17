<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProviderService extends Model
{
    use  HasFactory;

    protected $table = 'provider_service';
    protected $fillable = [
        'service_id',
        'provider_id'
    ];

    protected $casts = [
        'service_id' => 'integer',
        'provider_id' => 'integer'
    ];
}
