<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trip extends Model
{
    use HasFactory;

    protected $fillable = [
        'location',
        'duration',
        'budget',
        'photo_reference',
        'location_overview_id'
    ];
}
