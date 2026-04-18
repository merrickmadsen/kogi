<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recipe extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'base_servings',
        'prep_time',
        'cook_time',
        'cuisine_tags',
        'photo',
        'source',
        'notes',
    ];
    
    public function ingredients()
    {
        return $this->hasMany(RecipeIngredient::class);
    }
    
    public function instructions()
    {
        return $this->hasMany(RecipeInstruction::class)->orderBy('step_number');
    }
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
