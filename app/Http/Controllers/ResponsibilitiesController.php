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
    public function index() {
        $editor = DB::table('users')
            ->where('users.id', Auth::user()->id)
            ->join('user_relations', 'users.id', '=', 'user_relations.id_user')
            ->join('companies', 'companies.id', '=', 'user_relations.id_company')
            ->select('users.*', 'companies.nome_fantasia AS company', 'companies.tipo AS tipo', 'user_relations.is_manager AS is_manager', 'companies.id as id_company')
            ->first();
        
        $responsibilities = DB::table('responsibilities')
            ->join('companies', 'companies.id', '=', 'responsibilities.id_company')
            ->join('company_relations', function($join) {
                $join
                    ->on('companies.id', '=', 'company_relations.id_contratada')
                    ->orOn('companies.id', '=', 'company_relations.id_contratante');
            })
            ->select('responsibilities.*', 'companies.nome_fantasia AS company', 'companies.tipo AS tipo')
            ->paginate(9);
        
        if (Auth::user()->type == 'Administrador') {
            $responsibilities = DB::table('responsibilities')
                ->join('companies', 'companies.id', '=', 'responsibilities.id_company')
                ->select('responsibilities.*', 'companies.nome_fantasia AS company', 'companies.tipo AS tipo')
                ->paginate(9);
        }
        return view('responsibilities.index', compact('responsibilities', 'editor'));
    }
    
    public function create() {
        $editor = DB::table('users')
            ->where('users.id', Auth::user()->id)
            ->join('user_relations', 'users.id', '=', 'user_relations.id_user')
            ->join('companies', 'companies.id', '=', 'user_relations.id_company')
            ->select('users.*', 'companies.nome_fantasia AS company', 'companies.tipo AS tipo', 'user_relations.is_manager AS is_manager', 'companies.id as id_company')
            ->first();

        $documents = DB::table('documents')
            ->join('companies', 'companies.id', '=', 'documents.id_company')
            ->join('company_relations', function($join) {
                $join
                    ->on('companies.id', '=', 'company_relations.id_contratada')
                    ->orOn('companies.id', '=', 'company_relations.id_contratante');
            })
            ->select('documents.*', 'companies.nome_fantasia AS company', 'companies.tipo AS tipo')
            ->get();
        
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
        return view('responsibilities.create', compact('companies', 'documents'));
    }
    
    public function store(StoreResponsibilityRequest $request) {
        $editor = DB::table('users')
            ->where('users.id', Auth::user()->id)
            ->join('user_relations', 'users.id', '=', 'user_relations.id_user')
            ->join('companies', 'companies.id', '=', 'user_relations.id_company')
            ->select('users.*', 'companies.nome_fantasia AS company', 'companies.tipo AS tipo', 'user_relations.is_manager AS is_manager', 'companies.id as id_company')
            ->first();

        $req = $request->validated();
        
        $new_responsibility = Responsibility::create($req);

        return redirect()->route('responsibilities.index');
    }
    
    public function show(Responsibility $responsibility) {
        $responsibility = DB::table('responsibilities')
            ->where('responsibilities.id', $responsibility->id)
            ->first();
    
        return view('responsibilities.show', compact('responsibility'));
    }
    
    public function edit(Responsibility $responsibility) {
        $responsibility = DB::table('responsibilities')
            ->where('responsibilities.id', $responsibility->id)
            ->first();
            $editor = DB::table('users')
            ->where('users.id', Auth::user()->id)
            ->join('user_relations', 'users.id', '=', 'user_relations.id_user')
            ->join('companies', 'companies.id', '=', 'user_relations.id_company')
            ->select('users.*', 'companies.nome_fantasia AS company', 'companies.tipo AS tipo', 'user_relations.is_manager AS is_manager', 'companies.id as id_company')
            ->first();
        $documents = DB::table('documents')
            ->join('companies', 'companies.id', '=', 'documents.id_company')
            ->join('company_relations', function($join) {
                $join
                    ->on('companies.id', '=', 'company_relations.id_contratada')
                    ->orOn('companies.id', '=', 'company_relations.id_contratante');
            })
            ->select('documents.*', 'companies.nome_fantasia AS company', 'companies.tipo AS tipo')
            ->paginate(9);
        
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
        return view('responsibilities.edit', compact('responsibility', 'companies', 'documents'));
    }
    
    public function update(UpdateResponsibilityRequest $request, Responsibility $responsibility) {
        $req = $request->validated();

        $responsibility->update($req);

        return redirect()->route('responsibilities.index');
    }
    
    public function destroy(Responsibility $responsibility) {
        $employees = Employee::all();

        foreach ($employees as $employee) {
            if ($employee->id_responsibility == $responsibility->id) throw ValidationException::withMessages(['cnpj' => 'O campo cnpj tem um formato inválido.!']);
        }

        Responsibility::where('id', $responsibility->id)->delete();
        
        return redirect()->route('responsibilities.index');
    }
}