<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $fillable = [
        'razao_social',
        'nome_fantasia',
        'atividade_principal',
        'cnae',
        'endereco',
        'bairro',
        'cep',
        'cidade',
        'telefone',
        'email',
        'endereco',
        'cnpj',
        'id_manager',
    ];

    protected $appends = [
        'profile_photo_url',
    ];
}