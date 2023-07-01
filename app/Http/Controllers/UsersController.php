<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Requests\StoreUserRequest;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Company;
use App\Models\User_relation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class UsersController extends Controller
{
    public function index()
    {
        $editor = DB::table('users')
            ->where('users.id', Auth::user()->id)
            ->join('user_relations', 'users.id', '=', 'user_relations.id_user')
            ->join('companies', 'companies.id', '=', 'user_relations.id_company')
            ->select('users.*', 'companies.nome_fantasia AS company', 'user_relations.is_manager AS is_manager')
            ->first();
        $users = DB::table('users')
            ->join('user_relations', 'users.id', '=', 'user_relations.id_user')
            ->join('companies', 'companies.id', '=', 'user_relations.id_company')
            ->where('companies.nome_fantasia', $editor->company)
            ->where('users.active', 1)
            ->select('users.*', 'companies.nome_fantasia AS company', 'user_relations.is_manager AS is_manager')
            ->orderBy('type')
            ->paginate(9);
        $editor = DB::table('users')
            ->where('users.id', Auth::user()->id)
            ->join('user_relations', 'users.id', '=', 'user_relations.id_user')
            ->join('companies', 'companies.id', '=', 'user_relations.id_company')
            ->select('users.*', 'companies.nome_fantasia AS company', 'user_relations.is_manager AS is_manager')
            ->first();
        // abort_if(Gate::denies('user_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        // $users = User::where('company', Auth::user()->company)->paginate(9);
        if (Auth::user()->type == 'Administrador'){
            $users = DB::table('users')
                ->join('user_relations', 'users.id', '=', 'user_relations.id_user')
                ->join('companies', 'companies.id', '=', 'user_relations.id_company')
                ->select('users.*', 'companies.nome_fantasia AS company', 'user_relations.is_manager AS is_manager')
                ->orderBy('type')
                ->paginate(9);
        }

        return view('users.index', compact('users', 'editor'));
    }

    public function create()
    {
        if(!(Auth::user()->type == 'Moderador' || Auth::user()->type == 'Administrador')){
            abort(403, 'Access denied');
        }
        $companies = Company::all();
        $editor = DB::table('users')
            ->where('users.id', Auth::user()->id)
            ->join('user_relations', 'users.id', '=', 'user_relations.id_user')
            ->join('companies', 'companies.id', '=', 'user_relations.id_company')
            ->select('users.*', 'companies.nome_fantasia AS company', 'user_relations.is_manager AS is_manager')
            ->first();
        // abort_if(Gate::denies('user_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        
        return view('users.create', compact('companies', 'editor'));
    }

    public function store(StoreUserRequest $request)
    {
        if(!(Auth::user()->type == 'Moderador' || Auth::user()->type == 'Administrador')){
            abort(403, 'Access denied');
        }
        $req = $request->validated();
        if(Auth::user()->type == 'Moderador'){
            if($req["type"] == 'Administrador'){
                throw ValidationException::withMessages(['erro' => 'Você não tem permissão para isso!']);
            }
            // $req["company"] = Auth::user()->company;
            // init - checa se o usuário a ser criado faz parte da empresa do editor
            $editor = DB::table('users')
                ->where('users.id', Auth::user()->id)
                ->join('user_relations', 'users.id', '=', 'user_relations.id_user')
                ->join('companies', 'companies.id', '=', 'user_relations.id_company')
                ->select('users.*', 'companies.nome_fantasia AS company', 'user_relations.is_manager AS is_manager', 'companies.id AS id_company' )
                ->first();
            if($editor->id_company != (int) $req['company']){
                throw ValidationException::withMessages(['erro' => 'Você não tem permissão para isso!']);
            }
            // end - checa se o usuário a ser criado faz parte da empresa do editor
        }
        
        $temp_company = (int) $req['company'];
        unset($req['company']);
        $user = User::create($req);

        // init - cria empresa do usuário
        $relation_model = array(
            'id_company' => $temp_company,
            'id_user' => $user->id,
            'is_manager' => 0,
        );
        $relation = User_relation::create($relation_model);
        // end - cria empresa do usuário

        
        // $user->roles()->sync($request->input('roles', []));

        return redirect()->route('users.index');
    }

    public function show(User $user)
    {
        $user = DB::table('users')
            ->where('users.id', $user->id)
            ->join('user_relations', 'users.id', '=', 'user_relations.id_user')
            ->join('companies', 'companies.id', '=', 'user_relations.id_company')
            ->select('users.*', 'companies.nome_fantasia AS company', 'user_relations.is_manager AS is_manager')
            ->first();
        $editor = DB::table('users')
            ->where('users.id', Auth::user()->id)
            ->join('user_relations', 'users.id', '=', 'user_relations.id_user')
            ->join('companies', 'companies.id', '=', 'user_relations.id_company')
            ->select('users.*', 'companies.nome_fantasia AS company', 'user_relations.is_manager AS is_manager')
            ->first();
        // abort_if(Gate::denies('user_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('users.show', compact('user', 'editor'));
    }

    public function edit(User $user)
    {
        if(!(Auth::user()->type == 'Moderador' || Auth::user()->type == 'Administrador')){
            abort(403, 'Access denied');
        }
        $companies = Company::all();
        $user = DB::table('users')
            ->where('users.id', $user->id)
            ->join('user_relations', 'users.id', '=', 'user_relations.id_user')
            ->join('companies', 'companies.id', '=', 'user_relations.id_company')
            ->select('users.*', 'companies.nome_fantasia AS company', 'user_relations.is_manager AS is_manager')
            ->first();
        $editor = DB::table('users')
            ->where('users.id', Auth::user()->id)
            ->join('user_relations', 'users.id', '=', 'user_relations.id_user')
            ->join('companies', 'companies.id', '=', 'user_relations.id_company')
            ->select('users.*', 'companies.nome_fantasia AS company', 'user_relations.is_manager AS is_manager')
            ->first();
        // abort_if(Gate::denies('user_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        // $roles = Role::pluck('title', 'id');

        // $user->load('roles');

        return view('users.edit', compact('user', 'companies', 'editor'));
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        if(!(Auth::user()->type == 'Moderador' || Auth::user()->type == 'Administrador')){
            abort(403, 'Access denied');
        }
        $req = $request->validated();
        if(Auth::user()->type == 'Moderador'){
            // init - checa se o usuário a ser editado vai ser administrador
            if($req["type"] == 'Administrador'){
                throw ValidationException::withMessages(['erro' => 'Você não tem permissão para isso!']);
            }
            // end - checa se o usuário a ser editado vai ser administrador

            // init - checa se o usuário a ser editado é administrador
            $user_check = User::where('id', $user->id)->first();
            if($user_check->type == 'Administrador'){
                throw ValidationException::withMessages(['erro' => 'Você não tem permissão para isso!']);
            }
            // end - checa se o usuário a ser editado é administrador

            // init - checa se o usuário a ser editado faz parte da empresa do editor
            $editor = DB::table('users')
                ->where('users.id', Auth::user()->id)
                ->join('user_relations', 'users.id', '=', 'user_relations.id_user')
                ->join('companies', 'companies.id', '=', 'user_relations.id_company')
                ->select('users.*', 'companies.nome_fantasia AS company', 'user_relations.is_manager AS is_manager', 'companies.id AS id_company')
                ->first();
            if($editor->id_company !== (int) $req['company']){
                throw ValidationException::withMessages(['erro' => 'Você não tem permissão para isso!']);
            }
            // end - checa se o usuário a ser editado faz parte da empresa do editor
        }
        
        // init - checa se a senha nao foi alterada
        $req["password"] = $req["password"] == null ? $user->password : Hash::make($req["password"]);
        // end - checa se a senha nao foi alterada

        // init - checa se a empresa foi alterada
        $user_check = DB::table('users')
            ->where('users.id', $user->id)
            ->join('user_relations', 'users.id', '=', 'user_relations.id_user')
            ->join('companies', 'companies.id', '=', 'user_relations.id_company')
            ->select('users.*', 'companies.nome_fantasia AS company', 'user_relations.is_manager AS is_manager')
            ->first();
        $company = DB::table('companies')
            ->where('companies.nome_fantasia', $user_check->company)
            ->join('user_relations', 'user_relations.id_company', '=', 'companies.id')
            ->join('users', 'user_relations.id_user', '=', 'users.id')
            ->where('user_relations.is_manager', 1)
            ->select('companies.*', 'users.id AS id_manager')
            ->first();
        if((int) $req['company'] != $company->id){
            throw ValidationException::withMessages(['erro' => 'Você não tem permissão para isso!']);
        }
        // end - checa se a empresa foi alterada
    
        // init - altera empresa do usuario
        $relations = DB::table('user_relations')
            ->where('user_relations.id_user', $user->id)
            ->join('users', 'users.id', '=', 'user_relations.id_user')
            ->select('user_relations.*')
            ->first();
        // $relations->id_company = $req['company'];
        
        $relations = User_relation::findOrFail($relations->id);
        $relations->id_company = (int) $req['company'];
        
        $relations->update();
        // end - altera empresa do usuario

        // init - altera usuario
        unset($req['company']);
        $user->update($req);
        // end - altera usuario

        // $user->roles()->sync($request->input('roles', []));

        return redirect()->route('users.index');
    }

    public function destroy(User $user)
    {
        if(!(Auth::user()->type == 'Moderador' || Auth::user()->type == 'Administrador')){
            abort(403, 'Access denied');
        }
        // abort_if(Gate::denies('user_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        // $user->active = 0;
        $user->active = 0;
        $user->save();
        return redirect()->route('users.index');
    }
}
