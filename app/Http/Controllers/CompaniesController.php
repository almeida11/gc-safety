<?php

namespace App\Http\Controllers;

use Illuminate\Validation\ValidationException;
use App\Http\Requests\UpdateCompanyRequest;
use App\Http\Requests\StoreCompanyRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Helpers\ValidationsHelper;
use App\Helpers\PaginationHelper;
use App\Models\Company_relation;
use App\Models\User_relation;
use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\User;

class CompaniesController extends Controller {
    public function index() {
        $editor = DB::table('users')
            ->where('users.id', Auth::user()->id)
            ->join('user_relations', 'users.id', '=', 'user_relations.id_user')
            ->join('companies', 'companies.id', '=', 'user_relations.id_company')
            ->select('users.*', 'companies.nome_fantasia AS company', 'companies.tipo AS tipo', 'user_relations.is_manager AS is_manager', 'companies.id as id_company')
            ->first();
        
        $users = DB::table('users')
            ->join('user_relations', 'users.id', '=', 'user_relations.id_user')
            ->join('companies', 'companies.id', '=', 'user_relations.id_company')
            ->select('users.*', 'companies.nome_fantasia AS company', 'user_relations.is_manager AS is_manager')
            ->orderBy('type')
            ->get();
        
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

        $companies = PaginationHelper::paginate($companies, 9);

        if (Auth::user()->type == 'Administrador') {
            $companies = DB::table('companies')
                ->leftJoin('user_relations', function($join) {
                    $join->on('user_relations.id_company', '=', 'companies.id')
                    ->where('user_relations.is_manager', 1);
                })
                ->leftJoin('users', 'user_relations.id_user', '=', 'users.id')
                ->select('companies.*', 'users.id AS id_manager')
                ->paginate(9);
        }

        $pendencias = 0;

        $employees = DB::table('employees')
            ->get();

        $documents = DB::table('document_paths')
            ->get()->unique();

        $companies_doc_status = array();

        foreach ($employees as $employee) {
            if(json_decode($employee->documents)) {
                foreach (json_decode($employee->documents) as $document) {
                    $hasntDoc = true;
                    foreach ($documents as $document_name) {
                        if($document) {
                            if(($document_name->id_employee == $employee->id && $document_name->type == $document)) {
                                $hasntDoc = false;
                            }
                        }
                    }
                    if(($hasntDoc)) {
                        $check_test = true;
                        foreach ($companies_doc_status as $value) {
                            if($value['id'] == $employee->id_company) {
                                $check_test = false;
                            }
                        }
                        if($check_test) {
                            array_push($companies_doc_status, array ( 'id' => $employee->id_company, 'status' => false ));
                        }
                    }
                }
            }
        }

        return view('companies.index', compact('companies', 'users', 'editor', 'companies_doc_status'));
    }
    
    public function create() {
        $editor = DB::table('users')
            ->where('users.id', Auth::user()->id)
            ->join('user_relations', 'users.id', '=', 'user_relations.id_user')
            ->join('companies', 'companies.id', '=', 'user_relations.id_company')
            ->select('users.*', 'companies.nome_fantasia AS company', 'companies.tipo AS tipo', 'user_relations.is_manager AS is_manager')
            ->first();
            
        // if(!($editor->tipo == 'Contratante')) abort(404, 'Access denied');
        // if(!($editor->is_manager == 1)) abort(404, 'Access denied');
        // if(!(Auth::user()->type == 'Administrador')) abort(404, 'Access denied');

        $editor = DB::table('users')
            ->where('users.id', Auth::user()->id)
            ->join('user_relations', 'users.id', '=', 'user_relations.id_user')
            ->join('companies', 'companies.id', '=', 'user_relations.id_company')
            ->select('users.*', 'companies.nome_fantasia AS company', 'user_relations.is_manager AS is_manager')
            ->first();
        
        $users = DB::table('users')
            ->where('companies.nome_fantasia', $editor->company)
            ->join('user_relations', 'users.id', '=', 'user_relations.id_user')
            ->join('companies', 'companies.id', '=', 'user_relations.id_company')
            ->select('users.*', 'companies.nome_fantasia AS company', 'user_relations.is_manager AS is_manager')
            ->get();
            
        if (Auth::user()->type == 'Administrador') $users = User::all();

        return view('companies.create', compact('users', 'editor'));
    }

    public function store(StoreCompanyRequest $request) {
        if(!(Auth::user()->type == 'Moderador' || Auth::user()->type == 'Administrador')) abort(404, 'Access denied');

        $req = $request->validated();

        if(Auth::user()->type == 'Moderador') $req['tipo'] = 'Contratada';

        $manager = (int) $req['id_manager'];
        
        unset($req['id_manager']);

        $editor = DB::table('users')
            ->where('users.id', Auth::user()->id)
            ->join('user_relations', 'users.id', '=', 'user_relations.id_user')
            ->join('companies', 'companies.id', '=', 'user_relations.id_company')
            ->select('users.*', 'companies.nome_fantasia AS company', 'user_relations.is_manager AS is_manager')
            ->first();
        
        $company = DB::table('companies')
            ->where('user_relations.id_user', $editor->id)
            ->leftJoin('user_relations', function($join) {
                $join->on('user_relations.id_company', '=', 'companies.id')
                ->where('user_relations.is_manager', 1);
            })
            ->leftJoin('users', 'user_relations.id_user', '=', 'users.id')
            ->select('companies.*', 'users.id AS id_manager')
            ->first();
        
        $manager = DB::table('user_relations')
            ->where('user_relations.id_user', $manager)
            ->join('users', 'users.id', '=', 'user_relations.id_user')
            ->select('user_relations.*')
            ->first();
        
        $manager = User_relation::findOrFail($manager->id);

        $manager->is_manager = 1;

        $new_company = Company::create($req);

        $relation_model = array(
            'id_contratante' => $company->id,
            'id_contratada' => $new_company->id,
        );

        $new_relation = Company_relation::create($relation_model);

        $manager->id_company = $new_company->id;

        $manager->update();
        
        return redirect()->route('companies.index');
    }

    public function show(Company $company) { 
        $editor = DB::table('users')
            ->where('users.id', Auth::user()->id)
            ->join('user_relations', 'users.id', '=', 'user_relations.id_user')
            ->join('companies', 'companies.id', '=', 'user_relations.id_company')
            ->select('users.*', 'companies.nome_fantasia AS company', 'companies.tipo AS tipo', 'companies.id AS id_company', 'user_relations.is_manager AS is_manager')
            ->first();
        
        if(($editor->tipo == 'Contratada') && !($editor->company == $company->nome_fantasia)) abort(404, 'Access denied');

        $users = User::all();
        
        $company = DB::table('companies')
            ->where('companies.id', $company->id)
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
                ->join('company_relations', function($join) {
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
            if(!($editor->company == $company->nome_fantasia)) abort(404, 'Access denied');
        }
        
        return view('companies.show', compact('company', 'users', 'editor'));
    }

    public function edit(Company $company) {
        if(!(Auth::user()->type == 'Moderador' || Auth::user()->type == 'Administrador')) abort(404, 'Access denied');

        $editor = DB::table('users')
            ->where('users.id', Auth::user()->id)
            ->join('user_relations', 'users.id', '=', 'user_relations.id_user')
            ->join('companies', 'companies.id', '=', 'user_relations.id_company')
            ->select('users.*', 'companies.nome_fantasia AS company', 'user_relations.is_manager AS is_manager', 'companies.id AS id_company')
            ->first();
        
        $users = DB::table('users')
            ->join('user_relations', 'users.id', '=', 'user_relations.id_user')
            ->join('companies', 'companies.id', '=', 'user_relations.id_company')
            ->select('users.*', 'companies.nome_fantasia AS company', 'user_relations.is_manager AS is_manager')
            ->get();
        
        $company = DB::table('companies')
            ->where('companies.id', $company->id)
            ->leftJoin('user_relations', function($join) {
                $join->on('user_relations.id_company', '=', 'companies.id')
                ->where('user_relations.is_manager', 1);
            })
            ->leftJoin('users', 'user_relations.id_user', '=', 'users.id')
            ->select('companies.*', 'users.id AS id_manager')
            ->first();

        return view('companies.edit', compact('company', 'users', 'editor'));
    }
    
    public function update(UpdateCompanyRequest $request, Company $company) {
        if(!(Auth::user()->type == 'Administrador')) {
            $editor = DB::table('users')
                ->where('users.id', Auth::user()->id)
                ->where('user_relations.id_company', $company->id)
                ->join('user_relations', 'users.id', '=', 'user_relations.id_user')
                ->join('companies', 'companies.id', '=', 'user_relations.id_company')
                ->select('users.*', 'companies.nome_fantasia AS company', 'user_relations.is_manager AS is_manager', 'companies.id AS id_company')
                ->get();
            
            if(!(isset($editor->id))){ abort(404, 'Access denied');}

            if(!($editor->is_manager == 1)) abort(404, 'Access denied');
        }

        $req = $request->validated();

        if (!(ValidationsHelper::is_cnpj($req["cnpj"]))) throw ValidationException::withMessages(['cnpj' => 'O campo cnpj tem um formato inválido.!']);

        $manager = (int) $req['id_manager'];

        unset($req['id_manager']);

        if(isset($manager) && !(Auth::user()->type == 'Administrador')) {
            $new_manager = DB::table('user_relations')
                ->where('user_relations.id_user', $manager)
                ->where('user_relations.id_company', $company->id)
                ->join('users', 'users.id', '=', 'user_relations.id_user')
                ->select('user_relations.*')
                ->first();

            if(isset($new_manager)) {
                $old_manager = DB::table('user_relations')
                    ->where('user_relations.id_company', $company->id)
                    ->where('user_relations.is_manager', 1)
                    ->join('users', 'users.id', '=', 'user_relations.id_user')
                    ->select('user_relations.*')
                    ->first();
                if($old_manager->id_user != $manager) {
                    $old_manager = User_relation::findOrFail($old_manager->id);
                    $old_manager->is_manager = 0;
                    $old_manager->update();

                    
                    $new_manager = User_relation::findOrFail($new_manager->id);
                    $new_manager->is_manager = 1;
                    $new_manager->update();
                }
            } else {
                throw ValidationException::withMessages(['erro' => 'Usuario não faz parte da Empresa!']);
            }
        } else {
            $new_manager = DB::table('user_relations')
                ->where('user_relations.id_user', $manager)
                ->join('users', 'users.id', '=', 'user_relations.id_user')
                ->select('user_relations.*')
                ->first();

            if(isset($new_manager)) {
                $new_manager = User_relation::findOrFail($new_manager->id);
                $new_manager->is_manager = 1;
                $new_manager->id_company = $company->id;
                $new_manager->update();
            }
        }

        $company->update($req);

        return redirect()->route('companies.index');
    }

    public function destroy(Company $company) {
        $company = DB::table('companies')
            ->where('companies.id', $company->id)
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
        if(!(Auth::user()->type == 'Moderador' || Auth::user()->type == 'Administrador') || (Auth::user()->type == 'Moderador' && $company->tipo == "Contratante")){
            abort(404, 'Access denied');
        }

        $editor = DB::table('users')
            ->where('users.id', Auth::user()->id)
            ->join('user_relations', 'users.id', '=', 'user_relations.id_user')
            ->join('companies', 'companies.id', '=', 'user_relations.id_company')
            ->select('users.*', 'companies.nome_fantasia AS company', 'companies.tipo AS tipo', 'companies.id AS id_company', 'user_relations.is_manager AS is_manager')
            ->first();
        
        if(($editor->tipo == 'Contratada') ) abort(404, 'Access denied');

        if(!($company->id_contratante == $editor->id_company)) abort(404, 'Access denied');

        $relations = DB::table('user_relations')
            ->where('user_relations.id_company', $company->id)
            ->first();
        
        if(isset($relations)) throw ValidationException::withMessages(['erro' => 'Empresa com funcionários ativos!']);
        
        $new_company = Company::findOrFail($company->id);
        
        $new_company->ativo = 0;

        $new_company->update();

        return redirect()->route('companies.index');
    }
}