<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Jetstream\HasProfilePhoto;

class Employee extends Model
{
    use HasFactory;
    use HasProfilePhoto;

    protected $fillable = [
        'name',
        'cpf',
        'admission',
        'id_responsibility',
        'id_sector',
        'id_company',
        'active',
    ];

    protected $appends = [
        'profile_photo_url',
    ];
}
