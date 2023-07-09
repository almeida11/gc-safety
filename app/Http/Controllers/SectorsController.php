<?php

namespace App\Http\Controllers;

use Illuminate\Validation\ValidationException;
use App\Http\Requests\UpdateSectorRequest;
use App\Http\Requests\StoreSectorRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Company_relation;
use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Company;
use App\Models\Sector;

class SectorsController extends Controller {
    public function index() {
        $editor = DB::table('users')
            ->where('users.id', Auth::user()->id)
            ->join('user_relations', 'users.id', '=', 'user_relations.id_user')
            ->join('companies', 'companies.id', '=', 'user_relations.id_company')
            ->select('users.*', 'companies.nome_fantasia AS company', 'companies.tipo AS tipo', 'user_relations.is_manager AS is_manager', 'companies.id as id_company')
            ->first();
        
        $sectors = DB::table('sectors')
            ->join('companies', 'companies.id', '=', 'sectors.id_company')
            ->join('company_relations', function($join) {
                $join
                    ->on('companies.id', '=', 'company_relations.id_contratada')
                    ->orOn('companies.id', '=', 'company_relations.id_contratante');
            })
            ->select('sectors.*', 'companies.nome_fantasia AS company', 'companies.tipo AS tipo')
            ->paginate(9);
        
        if (Auth::user()->type == 'Administrador') {
            $sectors = DB::table('sectors')
                ->join('companies', 'companies.id', '=', 'sectors.id_company')
                ->select('sectors.*', 'companies.nome_fantasia AS company', 'companies.tipo AS tipo')
                ->paginate(9);
        }
        return view('sectors.index', compact('sectors', 'editor'));
    }
    
    public function create() {
        $editor = DB::table('users')
            ->where('users.id', Auth::user()->id)
            ->join('user_relations', 'users.id', '=', 'user_relations.id_user')
            ->join('companies', 'companies.id', '=', 'user_relations.id_company')
            ->select('users.*', 'companies.nome_fantasia AS company', 'companies.tipo AS tipo', 'user_relations.is_manager AS is_manager', 'companies.id as id_company')
            ->first();
        
        if(Auth::user()->type == 'Administrador') {
            $companies = Company::all();
        } else {
            if($editor->tipo == 'Contratante') {
                $companies = DB::table('companies')
                    ->where('company_relations.id_contratante', $editor->id_company)
                    ->join('company_relations', function($join) {
                        $join
                            ->on('companies.id', '=', 'company_relations.id_contratada')
                            ->orOn('companies.id', '=', 'company_relations.id_contratante');
                    })
                    ->leftJoin('user_relations', function($join) {
                        $join->on('user_relations.id_company', '=', 'companies.id')
                        ->where('user_relations.is_manager', 1);
                    })
                    ->leftJoin('users', 'user_relations.id_user', '=', 'users.id')
                    ->select('companies.*', 'users.id AS id_manager', 'company_relations.id_contratante')
                    ->paginate(9)->unique();
            } else {
                $companies = DB::table('companies')
                    ->Where('company_relations.id_contratada', $editor->id_company)
                    ->join('company_relations', function($join) {
                        $join
                            ->on('companies.id', '=', 'company_relations.id_contratada');
                    })
                    ->leftJoin('user_relations', function($join) {
                        $join->on('user_relations.id_company', '=', 'companies.id')
                        ->where('user_relations.is_manager', 1);
                    })
                    ->leftJoin('users', 'user_relations.id_user', '=', 'users.id')
                    ->select('companies.*', 'users.id AS id_manager', 'company_relations.id_contratante')
                    ->paginate(9)->unique();              
            }
        }
        return view('sectors.create', compact('companies'));
    }
    
    public function store(StoreSectorRequest $request) {
        
        $req = $request->validated();

        $new_sector = Sector::create($req);

        return redirect()->route('sectors.index');
    }
    
    public function show(Sector $sector) {
        $sector = DB::table('sectors')
            ->where('sectors.id', $sector->id)
            ->first();
    
        return view('sectors.show', compact('sector'));
    }
    
    public function edit(Sector $sector) {
        $sector = DB::table('sectors')
            ->where('sectors.id', $sector->id)
            ->first();
            $editor = DB::table('users')
            ->where('users.id', Auth::user()->id)
            ->join('user_relations', 'users.id', '=', 'user_relations.id_user')
            ->join('companies', 'companies.id', '=', 'user_relations.id_company')
            ->select('users.*', 'companies.nome_fantasia AS company', 'companies.tipo AS tipo', 'user_relations.is_manager AS is_manager', 'companies.id as id_company')
            ->first();
        
        if(Auth::user()->type == 'Administrador') {
            $companies = Company::all();
        } else {
            if($editor->tipo == 'Contratante') {
                $companies = DB::table('companies')
                    ->where('company_relations.id_contratante', $editor->id_company)
                    ->join('company_relations', function($join) {
                        $join
                            ->on('companies.id', '=', 'company_relations.id_contratada')
                            ->orOn('companies.id', '=', 'company_relations.id_contratante');
                    })
                    ->leftJoin('user_relations', function($join) {
                        $join->on('user_relations.id_company', '=', 'companies.id')
                        ->where('user_relations.is_manager', 1);
                    })
                    ->leftJoin('users', 'user_relations.id_user', '=', 'users.id')
                    ->select('companies.*', 'users.id AS id_manager', 'company_relations.id_contratante')
                    ->paginate(9)->unique();
            } else {
                $companies = DB::table('companies')
                    ->Where('company_relations.id_contratada', $editor->id_company)
                    ->join('company_relations', function($join) {
                        $join
                            ->on('companies.id', '=', 'company_relations.id_contratada');
                    })
                    ->leftJoin('user_relations', function($join) {
                        $join->on('user_relations.id_company', '=', 'companies.id')
                        ->where('user_relations.is_manager', 1);
                    })
                    ->leftJoin('users', 'user_relations.id_user', '=', 'users.id')
                    ->select('companies.*', 'users.id AS id_manager', 'company_relations.id_contratante')
                    ->paginate(9)->unique();              
            }
        }
        return view('sectors.edit', compact('sector', 'companies'));
    }
    
    public function update(UpdateSectorRequest $request, Sector $sector) {
        $req = $request->validated();
        $sector->update($req);
        return redirect()->route('sectors.index');
    }
    
    public function destroy(Sector $sector) {

        $employees = Employee::all();

        foreach ($employees as $employee) {
            if ($employee->id_sector == $sector->id) throw ValidationException::withMessages(['cnpj' => 'O campo cnpj tem um formato inválido.!']);
        }

        Sector::where('id', $sector->id)->delete();
        
        return redirect()->route('sectors.index');
    }
}