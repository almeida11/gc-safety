<?php

namespace App\Http\Controllers;

use Illuminate\Validation\ValidationException;
use App\Http\Requests\UpdateCompanyRequest;
use App\Http\Requests\StoreCompanyRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Helpers\ValidationsHelper;
use App\Helpers\PaginationHelper;
use App\Models\Company_relation;
use App\Models\User_relation;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Company;
use App\Models\Invite;
use App\Models\User;

class CompaniesController extends Controller {
    public function index() {
        $busca = isset($_GET['query-companie']) ? $_GET['query-companie'] : '';
        $orderby = isset($_GET['order-companie']) ? $_GET['order-companie'] : 'id';
        $method = isset($_GET['method-companie']) ? $_GET['method-companie'] : 'asc';
        
        $editor = DB::table('users')
            ->where('users.id', Auth::user()->id)
            ->leftjoin('user_relations', 'users.id', '=', 'user_relations.id_user')
            ->leftjoin('companies', 'companies.id', '=', 'user_relations.id_company')
            ->select('users.*', 'companies.name AS company', 'companies.tipo AS tipo', 'user_relations.is_manager AS is_manager', 'companies.id as id_company')
            ->first();
        
        $users = DB::table('users')
            ->leftjoin('user_relations', 'users.id', '=', 'user_relations.id_user')
            ->leftjoin('companies', 'companies.id', '=', 'user_relations.id_company')
            ->select('users.*', 'companies.name AS company', 'user_relations.is_manager AS is_manager')
            ->orderBy('type')
            ->get();
        
        if($editor->tipo == 'Contratante') {
            $companies = DB::table('companies')
                ->where(function ($query) use ($editor) {
                            $query->where('company_relations.id_contratante', $editor->id_company)
                            ->orWhere('companies.id', $editor->id_company);
                        })
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
                ->where(function ($query) use ($busca) {
                    $query->where('companies.razao_social', 'LIKE', '%' . $busca . '%')
                    ->orWhere('companies.cnpj', 'LIKE', '%' . $busca . '%')
                    ->orWhere('companies.tipo', 'LIKE', '%' . $busca . '%')
                    ->orWhere('users.name', 'LIKE', '%' . $busca . '%');
                })
                ->select('companies.*', 'users.id AS id_manager', 'users.name AS manager', 'company_relations.id_contratante')
                ->orderBy($orderby, $method)
                ->paginate(9)->unique();
        } else {
            if($editor->tipo == 'Contratada') {
                $companies = DB::table('companies')
                    ->Where('company_relations.id_contratada', $editor->id_company)
                    ->leftjoin('company_relations', function($join) {
                        $join
                            ->on('companies.id', '=', 'company_relations.id_contratada');
                    })
                    ->leftJoin('user_relations', function($join) {
                        $join->on('user_relations.id_company', '=', 'companies.id')
                        ->where('user_relations.is_manager', 1);
                    })
                    ->leftJoin('users', 'user_relations.id_user', '=', 'users.id')
                    ->where(function ($query) use ($busca) {
                        $query->where('companies.razao_social', 'LIKE', '%' . $busca . '%')
                        ->orWhere('companies.cnpj', 'LIKE', '%' . $busca . '%')
                        ->orWhere('companies.tipo', 'LIKE', '%' . $busca . '%')
                        ->orWhere('users.name', 'LIKE', '%' . $busca . '%');
                    })
                    ->select('companies.*', 'users.id AS id_manager', 'users.name AS manager', 'company_relations.id_contratante')
                    ->orderBy($orderby, $method)
                    ->paginate(9)->unique();
            }
        }

        if(isset($companies)) {
            $companies = PaginationHelper::paginate($companies, 9);
        } else {
            $companies = null;
        }

        if (Auth::user()->type == 'Administrador') {
            $companies = DB::table('companies')
                ->leftJoin('user_relations', function($join) {
                    $join->on('user_relations.id_company', '=', 'companies.id')
                    ->where('user_relations.is_manager', 1);
                })
                ->leftJoin('users', 'user_relations.id_user', '=', 'users.id')
                ->where(function ($query) use ($busca) {
                    $query->where('companies.razao_social', 'LIKE', '%' . $busca . '%')
                    ->orWhere('companies.cnpj', 'LIKE', '%' . $busca . '%')
                    ->orWhere('companies.tipo', 'LIKE', '%' . $busca . '%')
                    ->orWhere('users.name', 'LIKE', '%' . $busca . '%');
                })
                ->select('companies.*', 'users.id AS id_manager', 'users.name AS manager')
                ->orderBy($orderby, $method)
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
                            if(($document_name->id_employee == $employee->id && $document_name->type == $document  && $document_name->status == 'Aprovado')) {
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

        $invites = DB::table('invites')
            ->where('invites.id_company', $editor->id_company)
            ->paginate(9,['*'],'invites');


        return view('companies.index', compact('companies', 'users', 'editor', 'companies_doc_status', 'busca', 'orderby', 'method', 'invites'));
    }
    
    public function create() {
        $editor = DB::table('users')
            ->where('users.id', Auth::user()->id)
            ->leftjoin('user_relations', 'users.id', '=', 'user_relations.id_user')
            ->leftjoin('companies', 'companies.id', '=', 'user_relations.id_company')
            ->select('users.*', 'companies.name AS company', 'companies.tipo AS tipo', 'user_relations.is_manager AS is_manager')
            ->first();
            
        if(!($editor->tipo == 'Contratante' || $editor->company == null)) abort(404, 'Access denied');
        if(!($editor->type == 'Administrador' || $editor->type == 'Cliente' || $editor->type == 'Prestador')) abort(404, 'Access denied');

        $editor = DB::table('users')
            ->where('users.id', Auth::user()->id)
            ->leftjoin('user_relations', 'users.id', '=', 'user_relations.id_user')
            ->leftjoin('companies', 'companies.id', '=', 'user_relations.id_company')
            ->select('users.*', 'companies.name AS company', 'user_relations.is_manager AS is_manager')
            ->first();
        if($editor->company == null) {
            $users = DB::table('users')
                ->where('companies.name', $editor->company)
                ->leftjoin('user_relations', 'users.id', '=', 'user_relations.id_user')
                ->leftjoin('companies', 'companies.id', '=', 'user_relations.id_company')
                ->select('users.*', 'companies.name AS company', 'user_relations.is_manager AS is_manager')
                ->get();
        } else {
            $users = DB::table('users')
                ->where('companies.name', $editor->company)
                ->leftjoin('user_relations', 'users.id', '=', 'user_relations.id_user')
                ->leftjoin('companies', 'companies.id', '=', 'user_relations.id_company')
                ->select('users.*', 'companies.name AS company', 'user_relations.is_manager AS is_manager')
                ->where('users.id', '<>', $editor->id)
                ->get();
        }
        if (Auth::user()->type == 'Administrador') $users = User::all();

        return view('companies.create', compact('users', 'editor'));
    }

    public function store(StoreCompanyRequest $request) {
        $editor = DB::table('users')
            ->where('users.id', Auth::user()->id)
            ->leftjoin('user_relations', 'users.id', '=', 'user_relations.id_user')
            ->leftjoin('companies', 'companies.id', '=', 'user_relations.id_company')
            ->select('users.*', 'companies.name AS company', 'companies.tipo AS tipo', 'user_relations.is_manager AS is_manager', 'companies.id as id_company')
            ->first();
        
            
        // $company = DB::table('companies')
        //     ->where('companies.id', $company_id)
        //     ->leftJoin('company_relations', function($join) {
        //         $join->on('companies.id', '=', 'company_relations.id_contratada')->orOn('companies.id', '=', 'company_relations.id_contratante');
        //     })
        //     ->leftJoin('user_relations', function($join) {
        //         $join->on('user_relations.id_company', '=', 'companies.id')
        //         ->where('user_relations.is_manager', 1);
        //     })
        //     ->leftJoin('users', 'user_relations.id_user', '=', 'users.id')
        //     ->select('companies.*', 'users.id AS id_manager', 'company_relations.id_contratante as id_contratante')
        //     ->first();
            
        // if($company->tipo == 'Contratada') {
        //     $contratante = DB::table('companies')
        //         ->where('companies.id', $company->id_contratante)
        //         ->leftjoin('company_relations', function($join) {
        //             $join->on('companies.id', '=', 'company_relations.id_contratada')->orOn('companies.id', '=', 'company_relations.id_contratante');
        //         })
        //         ->leftJoin('user_relations', function($join) {
        //             $join->on('user_relations.id_company', '=', 'companies.id')
        //             ->where('user_relations.is_manager', 1);
        //         })
        //         ->leftJoin('users', 'user_relations.id_user', '=', 'users.id')
        //         ->select('companies.*', 'users.id AS id_manager', 'company_relations.id_contratante as id_contratante')
        //         ->first();
            
        //     if(!(isset($contratante))) abort(404, 'Access denied');
        
        //     if(!($company->id_contratante == $editor->id_company) && !($company->id == $editor->id_company)) abort(404, 'Access denied');
        // } else {
        //     if(!($editor->company == $company->name)) abort(404, 'Access denied');
        // }
        if(!($editor->type == 'Administrador' || $editor->type == 'Cliente' || $editor->type == 'Prestador')) abort(404, 'Access denied');
        if(!($editor->type == 'Administrador' || $editor->type == 'Cliente' || $editor->type == 'Prestador')) abort(404, 'Access denied');

        $req = $request->validated();

        if($editor->company == null) {
            $req['tipo'] = 'Contratante';
            $invite = DB::table('invites')
                ->where('invites.used_by_user', $editor->id)
                ->first();
        }
        
        if (!(ValidationsHelper::is_cnpj($req["cnpj"]))) throw ValidationException::withMessages(['cnpj' => 'O campo cnpj tem um formato inválido.!']);

        if(Auth::user()->type == 'Moderador') $req['tipo'] = 'Contratada';
            
        if(isset($req['id_manager'])) {
            $manager = (int) $req['id_manager'];
            unset($req['id_manager']);
        }
        
        $new_company = Company::create($req);

        $comp = Company::findOrFail($new_company->id);
        if($comp->tipo == 'Contratada'){
            $relation_model = array(
                'id_contratante' => $editor->id_company,
                'id_contratada' => $new_company->id,
            );

            $new_relation = Company_relation::create($relation_model);
        }
        
        if(isset($manager)) {
            $manager_db = DB::table('user_relations')
                ->where('user_relations.id_user', $manager)
                ->leftjoin('users', 'users.id', '=', 'user_relations.id_user')
                ->select('user_relations.*')
                ->first();
            if($manager_db == null) {
                $relation_model = array(
                    'id_company' => $new_company->id,
                    'id_user' => $manager,
                    'is_manager' => 1,
                );

                $relation = User_relation::create($relation_model);
            } else {
                $relation = User_relation::findOrFail($manager_db->id);
                $relation->is_manager = 1;
                $relation->id_company = $new_company->id;
                $relation->update();
            }
        }

        if(isset($invite)) {
            $affected = DB::table('invites')
                ->where('id', $invite->id)
                ->update(['used_by_company' => $new_company->id]);
        }

        return redirect()->route('companies.index');
    }

    public function show(Company $company) { 
        $company_id = $company->id;

        $editor = DB::table('users')
            ->where('users.id', Auth::user()->id)
            ->leftjoin('user_relations', 'users.id', '=', 'user_relations.id_user')
            ->leftjoin('companies', 'companies.id', '=', 'user_relations.id_company')
            ->select('users.*', 'companies.name AS company', 'companies.tipo AS tipo', 'companies.id AS id_company', 'user_relations.is_manager AS is_manager')
            ->first();
        
        if(($editor->tipo == 'Contratada') && !($editor->company == $company->name) && !($editor->type == 'Administrador')) abort(404, 'Access denied');

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
        
            if(!($company->id_contratante == $editor->id_company) && !($company->id == $editor->id_company)  && !($editor->type == 'Administrador')) abort(404, 'Access denied');
        } else {
            if(!($editor->company == $company->name)  && !($editor->type == 'Administrador')) abort(404, 'Access denied');
        }
        
        return view('companies.show', compact('company', 'users', 'editor', 'company_id'));
    }

    public function edit(Company $company) {

        $company_id = $company->id;


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
            
            if(!(isset($contratante))   && !($editor->type == 'Administrador')) abort(404, 'Access denied');
        
            if(!($company->id_contratante == $editor->id_company) && !($company->id == $editor->id_company)   && !($editor->type == 'Administrador')) abort(404, 'Access denied');
        } else {
            if(!($editor->company == $company->name)   && !($editor->type == 'Administrador')) abort(404, 'Access denied');
        }
        if(!($editor->type == 'Administrador' || $editor->type == 'Cliente' || $editor->type == 'Prestador')) abort(404, 'Access denied');
        $users = DB::table('users')
            ->leftjoin('user_relations', 'users.id', '=', 'user_relations.id_user')
            ->leftjoin('companies', 'companies.id', '=', 'user_relations.id_company')
            ->select('users.*', 'companies.name AS company', 'user_relations.is_manager AS is_manager')
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

        return view('companies.edit', compact('company', 'users', 'editor', 'company_id'));
    }
    
    public function update(UpdateCompanyRequest $request, Company $company) {
        $editor = DB::table('users')
            ->where('users.id', Auth::user()->id)
            ->leftjoin('user_relations', 'users.id', '=', 'user_relations.id_user')
            ->leftjoin('companies', 'companies.id', '=', 'user_relations.id_company')
            ->select('users.*', 'companies.name AS company', 'companies.tipo AS tipo', 'companies.id AS id_company', 'user_relations.is_manager AS is_manager')
            ->first();

        $company_check = DB::table('companies')
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
            
        if($company_check->tipo == 'Contratada') {
            $contratante = DB::table('companies')
                ->where('companies.id', $company_check->id_contratante)
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
            
            if(!(isset($contratante))   && !($editor->type == 'Administrador')) abort(404, 'Access denied');
        
            if(!($company_check->id_contratante == $editor->id_company) && !($company_check->id == $editor->id_company) && !($editor->type == 'Administrador')) abort(404, 'Access denied');
        } else {
            if(!($editor->company == $company_check->name)   && !($editor->type == 'Administrador')) abort(404, 'Access denied');
        }
        if(!($editor->type == 'Administrador' || $editor->type == 'Cliente' || $editor->type == 'Prestador')) abort(404, 'Access denied');
        if(!(Auth::user()->type == 'Administrador')) {
            $editor = DB::table('users')
                ->where('users.id', Auth::user()->id)
                ->leftjoin('user_relations', 'users.id', '=', 'user_relations.id_user')
                ->leftjoin('companies', 'companies.id', '=', 'user_relations.id_company')
                ->select('users.*', 'companies.name AS company', 'companies.tipo AS tipo', 'companies.id AS id_company', 'user_relations.is_manager AS is_manager')
                ->first();
            
            if(!(isset($editor->id))){
                abort(404, 'Access denied');
            } else {
                if(!($editor->type == "Moderador")) {
                    abort(404, 'Access denied');
                } else {
                    if(!($editor->tipo == "Contratante")) {
                        abort(404, 'Access denied');
                    } else {
                        $check = Company::findOrFail($company->id);
                        if(!($check->tipo == "Contratada")) {
                            abort(404, 'Access denied');
                        }
                    }
                }
            }
            
        }
        if($request->company_photo_path) {
            if(explode("/", $request->company_photo_path->getClientmimeType())[0] != 'image') {
                throw ValidationException::withMessages(['foto' => 'Você deve enviar somente arquivos de imagem.']);
            }

            $extension = $request->company_photo_path->getClientOriginalExtension();

            $path = 'documents/'.$company->name.'/empresa/';
            $path = preg_replace('/[ -]+/' , '_' , strtolower( preg_replace("[^a-zA-Z0-9-]", "-", strtr(utf8_decode(trim($path)), utf8_decode("áàãâéêíóôõúüñçÁÀÃÂÉÊÍÓÔÕÚÜÑÇ"), "aaaaeeiooouuncAAAAEEIOOOUUNC-")) ));

            $document_name = 'FOTO_' . $company->id. "_" . $company->name . ".{$extension}";
            $document_name = preg_replace('/[ -]+/' , '_' , strtolower( preg_replace("[^a-zA-Z0-9-]", "-", strtr(utf8_decode(trim($document_name)), utf8_decode("áàãâéêíóôõúüñçÁÀÃÂÉÊÍÓÔÕÚÜÑÇ"), "aaaaeeiooouuncAAAAEEIOOOUUNC-")) ));

            $request->company_photo_path->storeAs($path, $document_name);
        }

        $req = $request->validated();
        
        if($request->deleteProfilePhoto == 'deleteProfilePhoto') {
            Storage::disk('public')->delete($company->company_photo_path);
            $req['company_photo_path'] = null;
        }

        if($request->company_photo_path) {
            $req['company_photo_path'] = $path . $document_name;
        }

        if (!(ValidationsHelper::is_cnpj($req["cnpj"]))) throw ValidationException::withMessages(['cnpj' => 'O campo cnpj tem um formato inválido.!']);

        if(isset($req['id_manager'])) {
            $manager = (int) $req['id_manager'];

            unset($req['id_manager']);

            if(isset($manager) && !(Auth::user()->type == 'Administrador')) {
                $new_manager = DB::table('user_relations')
                    ->where('user_relations.id_user', $manager)
                    ->where('user_relations.id_company', $company->id)
                    ->leftjoin('users', 'users.id', '=', 'user_relations.id_user')
                    ->select('user_relations.*')
                    ->first();

                if(isset($new_manager)) {
                    $old_manager = DB::table('user_relations')
                        ->where('user_relations.id_company', $company->id)
                        ->where('user_relations.is_manager', 1)
                        ->leftjoin('users', 'users.id', '=', 'user_relations.id_user')
                        ->select('user_relations.*')
                        ->first();
                    if(isset($old_manager->id_user)) {
                        if($old_manager->id_user != $manager) {
                            $old_manager = User_relation::findOrFail($old_manager->id);
                            $old_manager->is_manager = 0;
                            $old_manager->update();
                            
                            $new_manager = User_relation::findOrFail($new_manager->id);
                            $new_manager->is_manager = 1;
                            $new_manager->update();
                        }
                    } else {
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
                    ->leftjoin('users', 'users.id', '=', 'user_relations.id_user')
                    ->select('user_relations.*')
                    ->first();
                $old_manager = DB::table('user_relations')
                    ->where('user_relations.id_company', $company->id)
                    ->where('user_relations.is_manager', 1)
                    ->leftjoin('users', 'users.id', '=', 'user_relations.id_user')
                    ->select('user_relations.*')
                    ->first();
                if(isset($new_manager)) {
                    $old_manager = User_relation::findOrFail($old_manager->id);
                    $old_manager->is_manager = 0;
                    $old_manager->update();

                    $new_manager = User_relation::findOrFail($new_manager->id);
                    $new_manager->is_manager = 1;
                    $new_manager->id_company = $company->id;
                    $new_manager->update();
                }
            }
        }

        $company->update($req);

        return redirect()->route('companies.index');
    }

    public function destroy(Company $company) {
        $editor = DB::table('users')
            ->where('users.id', Auth::user()->id)
            ->leftjoin('user_relations', 'users.id', '=', 'user_relations.id_user')
            ->leftjoin('companies', 'companies.id', '=', 'user_relations.id_company')
            ->select('users.*', 'companies.name AS company', 'companies.tipo AS tipo', 'companies.id AS id_company', 'user_relations.is_manager AS is_manager')
            ->first();
        
            
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
            
            if(!(isset($contratante))  && !($editor->type == 'Administrador')) abort(404, 'Access denied');
        
            if(!($company->id_contratante == $editor->id_company) && !($company->id == $editor->id_company)  && !($editor->type == 'Administrador')) abort(404, 'Access denied');
        } else {
            if(!($editor->company == $company->name)  && !($editor->type == 'Administrador')) abort(404, 'Access denied');
        }
        if(!($editor->type == 'Administrador' || $editor->type == 'Cliente' || $editor->type == 'Prestador')) abort(404, 'Access denied');
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
            
        $editor = DB::table('users')
            ->where('users.id', Auth::user()->id)
            ->leftjoin('user_relations', 'users.id', '=', 'user_relations.id_user')
            ->leftjoin('companies', 'companies.id', '=', 'user_relations.id_company')
            ->select('users.*', 'companies.name AS company', 'companies.tipo AS tipo', 'companies.id AS id_company', 'user_relations.is_manager AS is_manager')
            ->first();
        
        if(($editor->tipo == 'Contratada') && !($editor->type == 'Administrador')) abort(404, 'Access denied');

        if(!($company->id_contratante == $editor->id_company)   && !($editor->type == 'Administrador')) abort(404, 'Access denied');

        $relations = DB::table('user_relations')
            ->where('user_relations.id_company', $company->id)
            ->first();
        
        if(isset($relations)) throw ValidationException::withMessages(['erro' => 'Empresa com funcionários ativos!']);
        
        $new_company = Company::findOrFail($company->id);
        
        $new_company->ativo = 0;

        $new_company->update();

        return redirect()->route('companies.index');
    }
    
    public function createInvite(Int $company_id, Request $request) {
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
            
        $editor = DB::table('users')
            ->where('users.id', Auth::user()->id)
            ->leftjoin('user_relations', 'users.id', '=', 'user_relations.id_user')
            ->leftjoin('companies', 'companies.id', '=', 'user_relations.id_company')
            ->select('users.*', 'companies.name AS company', 'companies.tipo AS tipo', 'companies.id AS id_company', 'user_relations.is_manager AS is_manager')
            ->first();
        $invite_model = array(
            'id_owner' => $editor->id,
            'id_company' => $company->id,
            'invite_code' => Str::random(20),
            'status' => 'Não utilizado',
        );
        $new_invite = Invite::create($invite_model);
        return redirect()->route('companies.index', 'new_invite=yes');
    }
}
