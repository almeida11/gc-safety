<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User_relation extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_company',
        'id_user',
        'is_manager',
    ];
}