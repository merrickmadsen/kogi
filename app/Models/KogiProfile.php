<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KogiProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'dietary_restrictions',
        'food_allergies',
        'skill_level',
        'pantry_staples',
        'kitchen_equipment',
    ];
}
