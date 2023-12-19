<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateResponsibilityRequest;
use App\Http\Requests\StoreResponsibilityRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Company_relation;
use App\Models\Responsibility;
use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Company;
use App\Models\Document;

class ResponsibilitiesController extends Controller {
    public function index(Int $company_id) {

        $busca = isset($_GET['query-responsibilities']) ? $_GET['query-responsibilities'] : '';
        $orderby = isset($_GET['order-companie']) ? $_GET['order-companie'] : 'id';
        $method = isset($_GET['method-companie']) ? $_GET['method-companie'] : 'asc';


        $editor = DB::table('users')
            ->where('users.id', Auth::user()->id)
            ->leftjoin('user_relations', 'users.id', '=', 'user_relations.id_user')
            ->leftjoin('companies', 'companies.id', '=', 'user_relations.id_company')
            ->select('users.*', 'companies.name AS company', 'companies.tipo AS tipo', 'companies.id AS id_company', 'user_relations.is_manager AS is_manager')
            ->first();
            
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
        $responsibilities = DB::table('responsibilities')
            ->leftjoin('companies', 'companies.id', '=', 'responsibilities.id_company')
            ->where('companies.id', $company_id)
            ->leftjoin('company_relations', function($join) {
                $join
                    ->on('companies.id', '=', 'company_relations.id_contratada')
                    ->orOn('companies.id', '=', 'company_relations.id_contratante');
            })
            ->where(function ($query) use ($busca) {
                $query->where('responsibilities.id', 'LIKE', '%' . $busca . '%')
                ->orWhere('responsibilities.name', 'LIKE', '%' . $busca . '%')
                ->orWhere('companies.name', 'LIKE', '%' . $busca . '%'); 
            })
            ->select('responsibilities.*', 'companies.name AS company', 'companies.tipo AS tipo')
            ->orderBy($orderby, $method)
            ->paginate(9);
        return view('responsibilities.index', compact('responsibilities', 'editor', 'company_id', 'busca', 'method', 'orderby'));
    }
    
    public function create(Int $company_id) {
        $editor = DB::table('users')
            ->where('users.id', Auth::user()->id)
            ->leftjoin('user_relations', 'users.id', '=', 'user_relations.id_user')
            ->leftjoin('companies', 'companies.id', '=', 'user_relations.id_company')
            ->select('users.*', 'companies.name AS company', 'companies.tipo AS tipo', 'companies.id AS id_company', 'user_relations.is_manager AS is_manager')
            ->first();
        if(!($editor->type == 'Administrador' || $editor->type == 'Cliente' || $editor->type == 'Prestador')) abort(404, 'Access denied');

        $documents = DB::table('documents')
            ->leftjoin('companies', 'companies.id', '=', 'documents.id_company')
            ->where('companies.id', $company_id)
            ->leftjoin('company_relations', function($join) {
                $join
                    ->on('companies.id', '=', 'company_relations.id_contratada')
                    ->orOn('companies.id', '=', 'company_relations.id_contratante');
            })
            ->select('documents.*', 'companies.name AS company', 'companies.tipo AS tipo')
            ->get();
        
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
        return view('responsibilities.create', compact('companies', 'documents', 'company_id'));
    }
    
    public function store(Int $company_id, StoreResponsibilityRequest $request) {
        $editor = DB::table('users')
            ->where('users.id', Auth::user()->id)
            ->leftjoin('user_relations', 'users.id', '=', 'user_relations.id_user')
            ->leftjoin('companies', 'companies.id', '=', 'user_relations.id_company')
            ->select('users.*', 'companies.name AS company', 'companies.tipo AS tipo', 'companies.id AS id_company', 'user_relations.is_manager AS is_manager')
            ->first();
        if(!($editor->type == 'Administrador' || $editor->type == 'Cliente' || $editor->type == 'Prestador')) abort(404, 'Access denied');

        $req = $request->validated();
        
        $new_responsibility = Responsibility::create($req);

        return redirect()->route('responsibilities.index', $company_id);
    }
    
    public function show(Int $company_id, Responsibility $responsibility) {
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
        $responsibility = DB::table('responsibilities')
            ->where('responsibilities.id', $responsibility->id)
            ->where('id_company', $company_id)
            ->first();
    
        if(!($responsibility)) abort(404, 'Access denied');
        return view('responsibilities.show', compact('responsibility', 'company_id'));
    }
    
    public function edit(Int $company_id, Responsibility $responsibility) {
        $responsibility = DB::table('responsibilities')
            ->where('responsibilities.id', $responsibility->id)
            ->first();
        $editor = DB::table('users')
            ->where('users.id', Auth::user()->id)
            ->leftjoin('user_relations', 'users.id', '=', 'user_relations.id_user')
            ->leftjoin('companies', 'companies.id', '=', 'user_relations.id_company')
            ->select('users.*', 'companies.name AS company', 'companies.tipo AS tipo', 'companies.id AS id_company', 'user_relations.is_manager AS is_manager')
            ->first();
        if(!($editor->type == 'Administrador' || $editor->type == 'Cliente' || $editor->type == 'Prestador')) abort(404, 'Access denied');
        
        $editor = DB::table('users')
            ->where('users.id', Auth::user()->id)
            ->leftjoin('user_relations', 'users.id', '=', 'user_relations.id_user')
            ->leftjoin('companies', 'companies.id', '=', 'user_relations.id_company')
            ->select('users.*', 'companies.name AS company', 'companies.tipo AS tipo', 'companies.id AS id_company', 'user_relations.is_manager AS is_manager')
            ->first();
        
        $documents = DB::table('documents')
            ->leftjoin('companies', 'companies.id', '=', 'documents.id_company')
            ->where('companies.id', $company_id)
            ->leftjoin('company_relations', function($join) {
                $join
                    ->on('companies.id', '=', 'company_relations.id_contratada')
                    ->orOn('companies.id', '=', 'company_relations.id_contratante');
            })
            ->select('documents.*', 'companies.name AS company', 'companies.tipo AS tipo')
            ->paginate(9);

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
        
        return view('responsibilities.edit', compact('responsibility', 'companies', 'documents', 'company_id'));
    }
    
    public function update(Int $company_id, UpdateResponsibilityRequest $request, Responsibility $responsibility) {
        $editor = DB::table('users')
            ->where('users.id', Auth::user()->id)
            ->leftjoin('user_relations', 'users.id', '=', 'user_relations.id_user')
            ->leftjoin('companies', 'companies.id', '=', 'user_relations.id_company')
            ->select('users.*', 'companies.name AS company', 'companies.tipo AS tipo', 'companies.id AS id_company', 'user_relations.is_manager AS is_manager')
            ->first();
        if(!($editor->type == 'Administrador' || $editor->type == 'Cliente' || $editor->type == 'Prestador')) abort(404, 'Access denied');
        $req = $request->validated();
        if(!(isset($req['documents']))) $req['documents'] = [];
        $responsibility->update($req);

        return redirect()->route('responsibilities.index', $company_id);
    }
    
    public function destroy(Int $company_id,Responsibility $responsibility) {
        $editor = DB::table('users')
            ->where('users.id', Auth::user()->id)
            ->leftjoin('user_relations', 'users.id', '=', 'user_relations.id_user')
            ->leftjoin('companies', 'companies.id', '=', 'user_relations.id_company')
            ->select('users.*', 'companies.name AS company', 'companies.tipo AS tipo', 'companies.id AS id_company', 'user_relations.is_manager AS is_manager')
            ->first();
        if(!($editor->type == 'Administrador' || $editor->type == 'Cliente' || $editor->type == 'Prestador')) abort(404, 'Access denied');
        $employees = Employee::all();

        foreach ($employees as $employee) {
            if ($employee->id_responsibility == $responsibility->id) throw ValidationException::withMessages(['cnpj' => 'O campo cnpj tem um formato inválido.!']);
        }

        Responsibility::where('id', $responsibility->id)->delete();
        
        return redirect()->route('responsibilities.index', $company_id);
    }
}
