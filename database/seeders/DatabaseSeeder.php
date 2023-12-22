<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\User_relation;
use App\Models\Employee;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {

        \DB::table('companies')->insert([
            [
                'razao_social' => 'GC Sistemas S/A',
                'name' => 'GC Sistemas',
                'tipo' => "Contratante",
            ],
            [
                'razao_social' => 'GC Safety S/A',
                'name' => 'GC Safety',
                'tipo' => 'Contratada',
            ]
        ]);
        \DB::table('users')->insert([
            [
                'name' => 'Pedro Gabriel Gigante Roseno Lima',
                'email' => 'gabriel.gigante@gcsistemas.com.br',
                'password' => \Hash::make('12345678'),
                'type' => 'Administrador',
                'active' => true,
            ],
            [
                'name' => 'Mateus de Almeida',
                'email' => 'mateus.almeida@gcsistemas.com.br',
                'password' => \Hash::make('12345678'),
                'type' => 'Administrador',
                'active' => true,
            ],
            [
                'name' => 'Pablo',
                'email' => 'pablo@gcsistemas.com.br',
                'password' => \Hash::make('12345678'),
                'type' => 'Moderador',
                'active' => true,
            ],
            [
                'name' => 'Vinicius',
                'email' => 'vinicius@gcsistemas.com.br',
                'password' => \Hash::make('12345678'),
                'type' => 'Moderador',
                'active' => true,
            ]
        ]);
        \DB::table('user_relations')->insert([
            [
                'id_company' => 1,
                'id_user' => 1,
                'is_manager' => 1,
            ],
            [
                'id_company' => 2,
                'id_user' => 2,
                'is_manager' => 1,
            ],
            [
                'id_company' => 1,
                'id_user' => 3,
                'is_manager' => 0,
            ],
            [
                'id_company' => 1,
                'id_user' => 4,
                'is_manager' => 0,
            ]
        ]);
        
        \DB::table('company_relations')->insert([
            [
                'id_contratante' => 1,
                'id_contratada' => 2,
            ]
        ]);
    }
}
