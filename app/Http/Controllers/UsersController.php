<?php

namespace App\Http\Controllers;

use Illuminate\Validation\ValidationException;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Requests\StoreUserRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\User_relation;
use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\User;

class UsersController extends Controller {
    public function index() {
        $editor = DB::table('users')
            ->where('users.id', Auth::user()->id)
            ->join('user_relations', 'users.id', '=', 'user_relations.id_user')
            ->join('companies', 'companies.id', '=', 'user_relations.id_company')
            ->select('users.*', 'companies.nome_fantasia AS company', 'companies.tipo AS tipo', 'companies.id as id_company', 'user_relations.is_manager AS is_manager')
            ->first();

        if(Auth::user()->type == 'Moderador') {
            if($editor->tipo == 'Contratante') {
                $users = DB::table('users')
                    ->join('user_relations', 'users.id', '=', 'user_relations.id_user')
                    ->join('companies', 'companies.id', '=', 'user_relations.id_company')
                    ->join('company_relations', function($join) {
                        $join
                            ->on('companies.id', '=', 'company_relations.id_contratada')
                                ->orOn('companies.id', '=', 'company_relations.id_contratante');
                    })
                    ->where('company_relations.id_contratante', $editor->id_company)
                    ->select('users.*', 'companies.nome_fantasia AS company', 'companies.id AS id_company', 'user_relations.is_manager AS is_manager')
                    ->orderBy('type')
                    ->orderBy('company')
                    ->paginate(9);
            } else {
                $users = DB::table('users')
                    ->join('user_relations', 'users.id', '=', 'user_relations.id_user')
                    ->join('companies', 'companies.id', '=', 'user_relations.id_company')
                    ->join('company_relations', function($join) {
                        $join
                            ->on('companies.id', '=', 'company_relations.id_contratada');
                    })
                    ->where('company_relations.id_contratada', $editor->id_company)
                    ->select('users.*', 'companies.nome_fantasia AS company', 'companies.id AS id_company', 'user_relations.is_manager AS is_manager')
                    ->orderBy('type')
                    ->orderBy('company')
                    ->paginate(9);
            }
        } else {
            if($editor->tipo == 'Contratante') {
                $users = DB::table('users')
                    ->join('user_relations', 'users.id', '=', 'user_relations.id_user')
                    ->join('companies', 'companies.id', '=', 'user_relations.id_company')
                    ->join('company_relations', function($join) {
                        $join
                            ->on('companies.id', '=', 'company_relations.id_contratada')
                                ->orOn('companies.id', '=', 'company_relations.id_contratante');
                    })
                    ->where('company_relations.id_contratante', $editor->id_company)
                    ->where('users.active', 1)
                    ->select('users.*', 'companies.nome_fantasia AS company', 'companies.id AS id_company', 'user_relations.is_manager AS is_manager')
                    ->orderBy('type')
                    ->orderBy('company')
                    ->paginate(9);
            } else {
                $users = DB::table('users')
                    ->join('user_relations', 'users.id', '=', 'user_relations.id_user')
                    ->join('companies', 'companies.id', '=', 'user_relations.id_company')
                    ->join('company_relations', function($join) {
                        $join
                            ->on('companies.id', '=', 'company_relations.id_contratada');
                    })
                    ->where('company_relations.id_contratada', $editor->id_company)
                    ->where('users.active', 1)
                    ->select('users.*', 'companies.nome_fantasia AS company', 'companies.id AS id_company', 'user_relations.is_manager AS is_manager')
                    ->orderBy('type')
                    ->orderBy('company')
                    ->paginate(9);
            }
        }
        $editor = DB::table('users')
            ->where('users.id', Auth::user()->id)
            ->join('user_relations', 'users.id', '=', 'user_relations.id_user')
            ->join('companies', 'companies.id', '=', 'user_relations.id_company')
            ->select('users.*', 'companies.nome_fantasia AS company', 'user_relations.is_manager AS is_manager')
            ->first();
            
        if (Auth::user()->type == 'Administrador') {
            $users = DB::table('users')
                ->join('user_relations', 'users.id', '=', 'user_relations.id_user')
                ->join('companies', 'companies.id', '=', 'user_relations.id_company')
                ->select('users.*', 'companies.nome_fantasia AS company', 'user_relations.is_manager AS is_manager')
                ->orderBy('type')
                ->paginate(9);
        }

        return view('users.index', compact('users', 'editor'));
    }

    public function create() {
        if(!(Auth::user()->type == 'Moderador' || Auth::user()->type == 'Administrador')) abort(403, 'Access denied');

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

        $editor = DB::table('users')
            ->where('users.id', Auth::user()->id)
            ->join('user_relations', 'users.id', '=', 'user_relations.id_user')
            ->join('companies', 'companies.id', '=', 'user_relations.id_company')
            ->select('users.*', 'companies.nome_fantasia AS company', 'user_relations.is_manager AS is_manager')
            ->first();
        
        return view('users.create', compact('companies', 'editor'));
    }

    public function store(StoreUserRequest $request) {
        if(!(Auth::user()->type == 'Moderador' || Auth::user()->type == 'Administrador')) abort(403, 'Access denied');

        $req = $request->validated();

        if(Auth::user()->type == 'Moderador') {
            if($req["type"] == 'Administrador') throw ValidationException::withMessages(['erro' => 'Você não tem permissão para isso!']);
            
            $editor = DB::table('users')
                ->where('users.id', Auth::user()->id)
                ->join('user_relations', 'users.id', '=', 'user_relations.id_user')
                ->join('companies', 'companies.id', '=', 'user_relations.id_company')
                ->select('users.*', 'companies.nome_fantasia AS company', 'user_relations.is_manager AS is_manager', 'companies.id AS id_company' )
                ->first();

            if($editor->id_company != (int) $req['company']){
                throw ValidationException::withMessages(['erro' => 'Você não tem permissão para isso!']);
            }
        }
        
        $temp_company = (int) $req['company'];

        unset($req['company']);

        $user = User::create($req);

        $relation_model = array(
            'id_company' => $temp_company,
            'id_user' => $user->id,
            'is_manager' => 0,
        );

        $relation = User_relation::create($relation_model);

        return redirect()->route('users.index');
    }

    public function show(User $user) {
        $user = DB::table('users')
            ->where('users.id', $user->id)
            ->join('user_relations', 'users.id', '=', 'user_relations.id_user')
            ->join('companies', 'companies.id', '=', 'user_relations.id_company')
            ->join('company_relations', function($join) {
                $join
                    ->on('companies.id', '=', 'company_relations.id_contratada')
                        ->orOn('companies.id', '=', 'company_relations.id_contratante');
            })
            ->select('users.*','company_relations.id_contratante', 'companies.nome_fantasia AS company', 'user_relations.is_manager AS is_manager')
            ->first();
        
        $editor = DB::table('users')
            ->where('users.id', Auth::user()->id)
            ->join('user_relations', 'users.id', '=', 'user_relations.id_user')
            ->join('companies', 'companies.id', '=', 'user_relations.id_company')
            ->select('users.*', 'companies.nome_fantasia AS company', 'companies.tipo AS tipo', 'user_relations.is_manager AS is_manager', 'companies.id as id_company')
            ->first();
        
        if(!($editor->company == $user->company || $editor->id_company == $user->id_contratante)) abort(403, 'Access denied');

        return view('users.show', compact('user', 'editor'));
    }

    public function edit(User $user) {
        if(!(Auth::user()->type == 'Moderador' || Auth::user()->type == 'Administrador')) abort(403, 'Access denied');

        $user = DB::table('users')
            ->where('users.id', $user->id)
            ->join('user_relations', 'users.id', '=', 'user_relations.id_user')
            ->join('companies', 'companies.id', '=', 'user_relations.id_company')
            ->join('company_relations', function($join) {
                $join
                    ->on('companies.id', '=', 'company_relations.id_contratada')
                        ->orOn('companies.id', '=', 'company_relations.id_contratante');
            })
            ->select('users.*','company_relations.id_contratante', 'companies.nome_fantasia AS company', 'user_relations.is_manager AS is_manager')
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

        if(!($editor->company == $user->company || $editor->id_company == $user->id_contratante)) abort(403, 'Access denied');
        
        return view('users.edit', compact('user', 'companies', 'editor'));
    }

    public function update(UpdateUserRequest $request, User $user) {
        $user_check = DB::table('users')
            ->where('users.id', $user->id)
            ->join('user_relations', 'users.id', '=', 'user_relations.id_user')
            ->join('companies', 'companies.id', '=', 'user_relations.id_company')
            ->join('company_relations', function($join) {
                $join
                    ->on('companies.id', '=', 'company_relations.id_contratada')
                        ->orOn('companies.id', '=', 'company_relations.id_contratante');
            })
            ->select('users.*', 'companies.id AS id_company','company_relations.id_contratante', 'companies.nome_fantasia AS company', 'user_relations.is_manager AS is_manager')
            ->first();
        
        $editor = DB::table('users')
            ->where('users.id', Auth::user()->id)
            ->join('user_relations', 'users.id', '=', 'user_relations.id_user')
            ->join('companies', 'companies.id', '=', 'user_relations.id_company')
            ->select('users.*', 'companies.nome_fantasia AS company', 'companies.tipo AS tipo', 'user_relations.is_manager AS is_manager', 'companies.id as id_company')
            ->first();
        
        if(!($editor->company == $user_check->company || $editor->id_company == $user_check->id_contratante)) abort(403, 'Access denied');

        if(!(Auth::user()->type == 'Moderador' || Auth::user()->type == 'Administrador')) abort(403, 'Access denied');

        $req = $request->validated();

        if($editor->id == $user_check->id) {
            if((int) $req['active'] != $user_check->active) throw ValidationException::withMessages(['erro' => 'Você não tem permissão para isso!']);
            if((int) $req['company'] != $user_check->id_company) throw ValidationException::withMessages(['erro' => 'Você não tem permissão para isso!']);
        }

        if(Auth::user()->type == 'Moderador') {
            if($req["type"] == 'Administrador') throw ValidationException::withMessages(['erro' => 'Você não tem permissão para isso!']);
            
            $user_check = User::where('id', $user->id)->first();

            if($user_check->type == 'Administrador') throw ValidationException::withMessages(['erro' => 'Você não tem permissão para isso!']);
        }
        
        $req["password"] = $req["password"] == null ? $user->password : Hash::make($req["password"]);

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
        $company_validated = false;

        foreach ($companies as $company) {
            if((int) $req['company'] == $company->id) {
                $company_validated = true;
                break;
            }
        }

        if(!($company_validated)) throw ValidationException::withMessages(['erro' => 'Você não tem permissão para isso!']);
        
        $relations = DB::table('user_relations')
            ->where('user_relations.id_user', $user->id)
            ->join('users', 'users.id', '=', 'user_relations.id_user')
            ->select('user_relations.*')
            ->first();
        
        $relations = User_relation::findOrFail($relations->id);
        $relations->id_company = (int) $req['company'];
        
        $relations->update();

        unset($req['company']);

        $user->update($req);

        return redirect()->route('users.index');
    }

    public function destroy(User $user) {
        if(!(Auth::user()->type == 'Moderador' || Auth::user()->type == 'Administrador' || Auth::user()->id != $user->id)) abort(403, 'Access denied');

        $user_check = DB::table('users')
            ->where('users.id', $user->id)
            ->join('user_relations', 'users.id', '=', 'user_relations.id_user')
            ->join('companies', 'companies.id', '=', 'user_relations.id_company')
            ->join('company_relations', function($join) {
                $join
                    ->on('companies.id', '=', 'company_relations.id_contratada')
                        ->orOn('companies.id', '=', 'company_relations.id_contratante');
            })
            ->select('users.*','company_relations.id_contratante', 'companies.tipo AS tipo', 'companies.nome_fantasia AS company', 'user_relations.is_manager AS is_manager')
            ->first();
        $editor = DB::table('users')
            ->where('users.id', Auth::user()->id)
            ->join('user_relations', 'users.id', '=', 'user_relations.id_user')
            ->join('companies', 'companies.id', '=', 'user_relations.id_company')
            ->select('users.*', 'companies.nome_fantasia AS company', 'companies.tipo AS tipo', 'user_relations.is_manager AS is_manager', 'companies.id as id_company')
            ->first();
        
        if($user_check->tipo == 'Contratante' && $user_check->is_manager == 1) abort(403, 'Access denied');

        if(!($editor->company == $user_check->company || $editor->id_company == $user_check->id_contratante )) abort(403, 'Access denied');
        
        $user->active = 0;

        $user->save();
        
        return redirect()->route('users.index');
    }
}
