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
    public function index(Int $company_id) {
        $editor = DB::table('users')
            ->where('users.id', Auth::user()->id)
            ->join('user_relations', 'users.id', '=', 'user_relations.id_user')
            ->join('companies', 'companies.id', '=', 'user_relations.id_company')
            ->select('users.*', 'companies.name AS company', 'companies.tipo AS tipo', 'user_relations.is_manager AS is_manager', 'companies.id as id_company')
            ->first();
        
        $sectors = DB::table('sectors')
            ->join('companies', 'companies.id', '=', 'sectors.id_company')
            ->where('companies.id', $company_id)
            ->join('company_relations', function($join) {
                $join
                    ->on('companies.id', '=', 'company_relations.id_contratada')
                    ->orOn('companies.id', '=', 'company_relations.id_contratante');
            })
            ->select('sectors.*', 'companies.name AS company', 'companies.tipo AS tipo')
            ->paginate(9);
        return view('sectors.index', compact('sectors', 'editor', 'company_id'));
    }
    
    public function create(Int $company_id) {
        $editor = DB::table('users')
            ->where('users.id', Auth::user()->id)
            ->join('user_relations', 'users.id', '=', 'user_relations.id_user')
            ->join('companies', 'companies.id', '=', 'user_relations.id_company')
            ->select('users.*', 'companies.name AS company', 'companies.tipo AS tipo', 'user_relations.is_manager AS is_manager', 'companies.id as id_company')
            ->first();
        
        $companies = DB::table('companies')
            ->where('companies.id', $company_id)
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
        return view('sectors.create', compact('companies', 'company_id'));
    }
    
    public function store(Int $company_id, StoreSectorRequest $request) {
        
        $req = $request->validated();

        $new_sector = Sector::create($req);

        return redirect()->route('sectors.index', $company_id);
    }
    
    public function show(Int $company_id, Sector $sector) {
        $sector = DB::table('sectors')
            ->where('sectors.id', $sector->id)
            ->first();
    
        return view('sectors.show', compact('sector', 'company_id'));
    }
    
    public function edit(Int $company_id, Sector $sector) {
        $sector = DB::table('sectors')
            ->where('sectors.id', $sector->id)
            ->first();
            $editor = DB::table('users')
            ->where('users.id', Auth::user()->id)
            ->join('user_relations', 'users.id', '=', 'user_relations.id_user')
            ->join('companies', 'companies.id', '=', 'user_relations.id_company')
            ->select('users.*', 'companies.name AS company', 'companies.tipo AS tipo', 'user_relations.is_manager AS is_manager', 'companies.id as id_company')
            ->first();
        
        $companies = DB::table('companies')
            ->where('companies.id', $company_id)
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
        return view('sectors.edit', compact('sector', 'companies', 'company_id'));
    }
    
    public function update(Int $company_id, UpdateSectorRequest $request, Sector $sector) {
        $req = $request->validated();
        $sector->update($req);
        return redirect()->route('sectors.index', $company_id);
    }
    
    public function destroy(Int $company_id, Sector $sector) {

        $employees = Employee::all();

        foreach ($employees as $employee) {
            if ($employee->id_sector == $sector->id) throw ValidationException::withMessages(['cnpj' => 'O campo cnpj tem um formato inválido.!']);
        }

        Sector::where('id', $sector->id)->delete();
        
        return redirect()->route('sectors.index', $company_id);
    }
}