<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Jetstream\HasProfilePhoto;

class Company extends Model
{
    use HasProfilePhoto;
    
    protected $fillable = [
        'razao_social',
        'name',
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
        'tipo',
        'id_manager',
        'ativo',
        'tipo',
        'company_photo_path',
    ];

    protected $appends = [
        'profile_photo_url',
    ];
}