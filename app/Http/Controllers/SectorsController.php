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

        $busca = isset($_GET['query-sector']) ? $_GET['query-sector'] : '';
        $orderby = isset($_GET['order-companie']) ? $_GET['order-companie'] : 'id';
        $method = isset($_GET['method-companie']) ? $_GET['method-companie'] : 'asc';

        $editor = DB::table('users')
            ->where('users.id', Auth::user()->id)
            ->leftjoin('user_relations', 'users.id', '=', 'user_relations.id_user')
            ->leftjoin('companies', 'companies.id', '=', 'user_relations.id_company')
            ->select('users.*', 'companies.name AS company', 'companies.tipo AS tipo', 'user_relations.is_manager AS is_manager', 'companies.id as id_company')
            ->first();
        if(($editor->tipo == 'Contratada') && !($editor->id_company == $company_id)) abort(404, 'Access denied');

        $company = DB::table('companies')
            ->where('companies.id', $company_id)
            ->leftJoin('company_relations', function($join) {
                $join->on('companies.id', '=', 'company_relations.id_contratada')->orOn('companies.id', '=', 'company_relations.id_contratante');
            })
            ->leftJoin('user_relations', function($join) {
                $join->on('user_relations.id_company', '=', 'companies.id')
                ->where('user_relations.is_manager', 1);
            })
            ->leftJoin('users', 'user_relations.id_user', '=', 'users.id')
            ->select('companies.*', 'users.id AS id_manager', 'company_relations.id_contratante as id_contratante')
            ->first();
            
        if($company->tipo == 'Contratada') {
            $contratante = DB::table('companies')
                ->where('companies.id', $company->id_contratante)
                ->leftjoin('company_relations', function($join) {
                    $join->on('companies.id', '=', 'company_relations.id_contratada')->orOn('companies.id', '=', 'company_relations.id_contratante');
                })
                ->leftJoin('user_relations', function($join) {
                    $join->on('user_relations.id_company', '=', 'companies.id')
                    ->where('user_relations.is_manager', 1);
                })
                ->leftJoin('users', 'user_relations.id_user', '=', 'users.id')
                ->select('companies.*', 'users.id AS id_manager', 'company_relations.id_contratante as id_contratante')
                ->first();
            
            if(!(isset($contratante))) abort(404, 'Access denied');
        
            if(!($company->id_contratante == $editor->id_company) && !($company->id == $editor->id_company)) abort(404, 'Access denied');
        } else {
            if(!($editor->company == $company->name)) abort(404, 'Access denied');
        }
        $sectors = DB::table('sectors')
            ->leftjoin('companies', 'companies.id', '=', 'sectors.id_company')
            ->where('companies.id', $company_id)
            ->leftjoin('company_relations', function($join) {
                $join
                    ->on('companies.id', '=', 'company_relations.id_contratada')
                    ->orOn('companies.id', '=', 'company_relations.id_contratante');
            })
            ->where(function ($query) use ($busca) {
                $query->where('sectors.id', 'LIKE', '%' . $busca . '%')
                ->orWhere('sectors.name', 'LIKE', '%' . $busca . '%')
                ->orWhere('companies.name', 'LIKE', '%' . $busca . '%');
            })
            ->select('sectors.*', 'companies.name AS company', 'companies.tipo AS tipo')
            ->orderBy($orderby, $method)
            ->paginate(9);
        return view('sectors.index', compact('sectors', 'editor', 'company_id', 'busca', 'method', 'orderby'));
    }
    
    public function create(Int $company_id) {
        $editor = DB::table('users')
            ->where('users.id', Auth::user()->id)
            ->leftjoin('user_relations', 'users.id', '=', 'user_relations.id_user')
            ->leftjoin('companies', 'companies.id', '=', 'user_relations.id_company')
            ->select('users.*', 'companies.name AS company', 'companies.tipo AS tipo', 'companies.id AS id_company', 'user_relations.is_manager AS is_manager')
            ->first();
        if(!($editor->type == 'Administrador' || $editor->type == 'Cliente' || $editor->type == 'Prestador')) abort(404, 'Access denied');
        
        $companies = DB::table('companies')
            ->where('companies.id', $company_id)
            ->leftjoin('company_relations', function($join) {
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
        $editor = DB::table('users')
            ->where('users.id', Auth::user()->id)
            ->leftjoin('user_relations', 'users.id', '=', 'user_relations.id_user')
            ->leftjoin('companies', 'companies.id', '=', 'user_relations.id_company')
            ->select('users.*', 'companies.name AS company', 'companies.tipo AS tipo', 'companies.id AS id_company', 'user_relations.is_manager AS is_manager')
            ->first();
        if(!($editor->type == 'Administrador' || $editor->type == 'Cliente' || $editor->type == 'Prestador')) abort(404, 'Access denied');
        $req = $request->validated();

        $new_sector = Sector::create($req);

        return redirect()->route('sectors.index', $company_id);
    }
    
    public function show(Int $company_id, Sector $sector) {
        $editor = DB::table('users')
            ->where('users.id', Auth::user()->id)
            ->leftjoin('user_relations', 'users.id', '=', 'user_relations.id_user')
            ->leftjoin('companies', 'companies.id', '=', 'user_relations.id_company')
            ->select('users.*', 'companies.name AS company', 'companies.tipo AS tipo', 'user_relations.is_manager AS is_manager', 'companies.id as id_company')
            ->first();
                if(($editor->tipo == 'Contratada') && !($editor->id_company == $company_id)) abort(404, 'Access denied');        if(($editor->tipo == 'Contratada') && !($editor->id_company == $company_id)) abort(404, 'Access denied');if(($editor->tipo == 'Contratada') && !($editor->id_company == $company_id)) abort(404, 'Access denied');

        $company = DB::table('companies')
            ->where('companies.id', $company_id)
            ->leftJoin('company_relations', function($join) {
                $join->on('companies.id', '=', 'company_relations.id_contratada')->orOn('companies.id', '=', 'company_relations.id_contratante');
            })
            ->leftJoin('user_relations', function($join) {
                $join->on('user_relations.id_company', '=', 'companies.id')
                ->where('user_relations.is_manager', 1);
            })
            ->leftJoin('users', 'user_relations.id_user', '=', 'users.id')
            ->select('companies.*', 'users.id AS id_manager', 'company_relations.id_contratante as id_contratante')
            ->first();
            
        if($company->tipo == 'Contratada') {
            $contratante = DB::table('companies')
                ->where('companies.id', $company->id_contratante)
                ->leftjoin('company_relations', function($join) {
                    $join->on('companies.id', '=', 'company_relations.id_contratada')->orOn('companies.id', '=', 'company_relations.id_contratante');
                })
                ->leftJoin('user_relations', function($join) {
                    $join->on('user_relations.id_company', '=', 'companies.id')
                    ->where('user_relations.is_manager', 1);
                })
                ->leftJoin('users', 'user_relations.id_user', '=', 'users.id')
                ->select('companies.*', 'users.id AS id_manager', 'company_relations.id_contratante as id_contratante')
                ->first();
            
            if(!(isset($contratante))) abort(404, 'Access denied');
        
            if(!($company->id_contratante == $editor->id_company) && !($company->id == $editor->id_company)) abort(404, 'Access denied');
        } else {
            if(!($editor->company == $company->name)) abort(404, 'Access denied');
        }
        $sector = DB::table('sectors')
            ->where('sectors.id', $sector->id)
            ->where('id_company', $company_id)
            ->first();
        if(!($sector)) abort(404, 'Access denied');
    
        return view('sectors.show', compact('sector', 'company_id'));
    }
    
    public function edit(Int $company_id, Sector $sector) {
        $editor = DB::table('users')
            ->where('users.id', Auth::user()->id)
            ->leftjoin('user_relations', 'users.id', '=', 'user_relations.id_user')
            ->leftjoin('companies', 'companies.id', '=', 'user_relations.id_company')
            ->select('users.*', 'companies.name AS company', 'companies.tipo AS tipo', 'companies.id AS id_company', 'user_relations.is_manager AS is_manager')
            ->first();
        if(!($editor->type == 'Administrador' || $editor->type == 'Cliente' || $editor->type == 'Prestador')) abort(404, 'Access denied');
        $sector = DB::table('sectors')
            ->where('sectors.id', $sector->id)
            ->first();
            $editor = DB::table('users')
            ->where('users.id', Auth::user()->id)
            ->leftjoin('user_relations', 'users.id', '=', 'user_relations.id_user')
            ->leftjoin('companies', 'companies.id', '=', 'user_relations.id_company')
            ->select('users.*', 'companies.name AS company', 'companies.tipo AS tipo', 'user_relations.is_manager AS is_manager', 'companies.id as id_company')
            ->first();
        
        $companies = DB::table('companies')
            ->where('companies.id', $company_id)
            ->leftjoin('company_relations', function($join) {
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
        $editor = DB::table('users')
            ->where('users.id', Auth::user()->id)
            ->leftjoin('user_relations', 'users.id', '=', 'user_relations.id_user')
            ->leftjoin('companies', 'companies.id', '=', 'user_relations.id_company')
            ->select('users.*', 'companies.name AS company', 'companies.tipo AS tipo', 'companies.id AS id_company', 'user_relations.is_manager AS is_manager')
            ->first();
        if(!($editor->type == 'Administrador' || $editor->type == 'Cliente' || $editor->type == 'Prestador')) abort(404, 'Access denied');
        $req = $request->validated();
        $sector->update($req);
        return redirect()->route('sectors.index', $company_id);
    }
    
    public function destroy(Int $company_id, Sector $sector) {
        $editor = DB::table('users')
            ->where('users.id', Auth::user()->id)
            ->leftjoin('user_relations', 'users.id', '=', 'user_relations.id_user')
            ->leftjoin('companies', 'companies.id', '=', 'user_relations.id_company')
            ->select('users.*', 'companies.name AS company', 'companies.tipo AS tipo', 'companies.id AS id_company', 'user_relations.is_manager AS is_manager')
            ->first();
        if(!($editor->type == 'Administrador' || $editor->type == 'Cliente' || $editor->type == 'Prestador')) abort(404, 'Access denied');

        $employees = Employee::all();

        foreach ($employees as $employee) {
            if ($employee->id_sector == $sector->id) throw ValidationException::withMessages(['cnpj' => 'O campo cnpj tem um formato inválido.!']);
        }

        Sector::where('id', $sector->id)->delete();
        
        return redirect()->route('sectors.index', $company_id);
    }
}
