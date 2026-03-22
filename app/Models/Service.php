<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; // ¡Importante!

class Service extends Model
{
    use HasFactory, SoftDeletes; // ¡Importante!

    protected $fillable = ['user_id', 'foto_persona'];
}
