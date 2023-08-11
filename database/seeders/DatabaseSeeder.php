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
        /* \App\Models\User::factory(10)->create();

         \App\Models\User::factory()->create([
             'name' => 'Test User',
             'email' => 'test@example.com',
          ]);
          
        'name',
        'email',
        'password',
        'type',
        'company',
        'active',
          */

        \DB::table('companies')->insert([
            [
                'razao_social' => 'Gigante Sistemas S/A',
                'name' => 'Gigante Sistemas',
                'tipo' => "Contratante",
            ],
            [
                'razao_social' => 'Mateus Sistemas S/A',
                'name' => 'Mateus Sistemas',
                'tipo' => 'Contratada',
            ]
        ]);
        \DB::table('users')->insert([
            [
                'name' => 'Pedro Gabriel Gigante Roseno Lima',
                'email' => 'gabriel.gigante@gigantesistemas.com.br',
                'password' => \Hash::make('12345678'),
                'type' => 'Administrador',
                'active' => true,
            ],
            [
                'name' => 'Mateus de Almeida',
                'email' => 'mateus.almeida@gigantesistemas.com.br',
                'password' => \Hash::make('12345678'),
                'type' => 'Administrador',
                'active' => true,
            ],
            [
                'name' => 'Pablo',
                'email' => 'pablo@gigantesistemas.com.br',
                'password' => \Hash::make('12345678'),
                'type' => 'Moderador',
                'active' => true,
            ],
            [
                'name' => 'Vinicius',
                'email' => 'vinicius@gigantesistemas.com.br',
                'password' => \Hash::make('12345678'),
                'type' => 'Moderador',
                'active' => true,
            ],
            [
                'name' => 'Teste',
                'email' => 'Teste@gigantesistemas.com.br',
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
            ],
            [
                'id_company' => 1,
                'id_user' => 5,
                'is_manager' => 0,
            ]
        ]);
        \DB::table('company_relations')->insert([
            [
                'id_contratante' => 1,
                'id_contratada' => 2,
            ]
        ]);
        // User::factory()->count(50)->create();
        // User_relation::factory()->count(50)->create();
        // Employee::factory()->count(50)->create();
    }
}
