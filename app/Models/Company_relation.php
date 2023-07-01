<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company_relation extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_contratada',
        'id_contratante',
    ];
}
