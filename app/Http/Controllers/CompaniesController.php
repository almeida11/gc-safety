<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreCompanyRequest;
use App\Models\Company;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\User_relation;
use App\Models\User;

class CompaniesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // abort_if(Gate::denies('user_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        // $users = User::where('company', Auth::user()->company)->get();
        // if (Auth::user()->type == 'Administrador'){
        //     $users = User::all();
        // }
        // $companies = Company::where('nome_fantasia', Auth::user()->company)->paginate(9); //
        $editor = DB::table('users')
                ->where('users.id', Auth::user()->id)
                ->join('user_relations', 'users.id', '=', 'user_relations.id_user')
                ->join('companies', 'companies.id', '=', 'user_relations.id_company')
                ->select('users.*', 'companies.nome_fantasia AS company', 'user_relations.is_manager AS is_manager')
                ->first();
        // esse acesso das empresas deve ser só pra ADM? ou Mods?
        $companies = Company::where('nome_fantasia', $editor->company)->paginate(9);
        if (Auth::user()->type == 'Administrador'){
            $companies = Company::orderBy('id')->paginate(9);
        }
        $users = DB::table('users')
                ->join('user_relations', 'users.id', '=', 'user_relations.id_user')
                ->join('companies', 'companies.id', '=', 'user_relations.id_company')
                ->select('users.*', 'companies.nome_fantasia AS company', 'user_relations.is_manager AS is_manager')
                ->paginate(9);
        return view('companies.index', compact('companies', 'users', 'editor'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if(!(Auth::user()->type == 'Moderador' || Auth::user()->type == 'Administrador')){
            abort(403, 'Access denied');
        }
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
        // abort_if(Gate::denies('user_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        // $users = User::where('company', Auth::user()->company)->paginate(9);
        if (Auth::user()->type == 'Administrador'){
            $users = User::all();
        }
        return view('companies.create', compact('users', 'editor'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCompanyRequest $request)
    {
        if(!(Auth::user()->type == 'Moderador' || Auth::user()->type == 'Administrador')){
            abort(403, 'Access denied');
        }

        $req = $request->validated();
        
        $company = Company::create($req);
        // $user->roles()->sync($request->input('roles', []));

        return redirect()->route('companies.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Company $company)
    {
        $users = User::all();
        return view('companies.show', compact('company', 'users'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Company $company)
    {
        if(!(Auth::user()->type == 'Moderador' || Auth::user()->type == 'Administrador')){
            abort(403, 'Access denied');
        }
        $editor = DB::table('users')
                ->where('users.id', Auth::user()->id)
                ->join('user_relations', 'users.id', '=', 'user_relations.id_user')
                ->join('companies', 'companies.id', '=', 'user_relations.id_company')
                ->select('users.*', 'companies.nome_fantasia AS company', 'user_relations.is_manager AS is_manager', 'companies.id AS id_company')
                ->first();
        // abort_if(Gate::denies('user_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        // $roles = Role::pluck('title', 'id');

        // $user->load('roles');
        $users = DB::table('users')
                ->join('user_relations', 'users.id', '=', 'user_relations.id_user')
                ->join('companies', 'companies.id', '=', 'user_relations.id_company')
                ->select('users.*', 'companies.nome_fantasia AS company', 'user_relations.is_manager AS is_manager')
                ->get();
        return view('companies.edit', compact('company', 'users', 'editor'));
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCompanyRequest $request, User $user)
    {
        if(!(Auth::user()->type == 'Moderador' || Auth::user()->type == 'Administrador')){
            abort(403, 'Access denied');
        }
        $req = $request->validated();
        dump($req);
        exit;
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
            if($editor->id_company !== $req['company']){
                throw ValidationException::withMessages(['erro' => 'Você não tem permissão para isso!']);
            }
            // end - checa se o usuário a ser editado faz parte da empresa do editor
        }

        // init - checa se a senha nao foi alterada
        $req["password"] = $req["password"] == null ? $user->password : Hash::make($req["password"]);
        // end - checa se a senha nao foi alterada

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

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $company)
    {
        if(!(Auth::user()->type == 'Moderador' || Auth::user()->type == 'Administrador')){
            abort(403, 'Access denied');
        }

        $relations = DB::table('user_relations')
            ->where('user_relations.id_company', $company)
            ->first();;
        dump($relations);
        exit;

        // abort_if(Gate::denies('user_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        // $user->active = 0;
        $user->active = 0;
        $user->save();
        return redirect()->route('users.index');
    }
}
