<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document_path extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'actual',
        'status',
        'due_date',
        'path',
        'id_employee',
    ];
}