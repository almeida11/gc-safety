<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Jetstream\HasProfilePhoto;

class Invite extends Model
{
    use HasFactory;
    use HasProfilePhoto;

    protected $fillable = [
        'id_owner',
        'id_company',
        'used_by_user',
        'used_by_company',
        'invite_code',
        'status',
    ];
}
