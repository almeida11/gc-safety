<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreCompanyRequest;
use App\Http\Requests\UpdateCompanyRequest;
use App\Models\Company;
use App\Models\Company_relation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\User_relation;
use App\Models\User;
use App\Helpers\PaginationHelper;
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
        $companies = DB::table('companies')
            ->where('nome_fantasia', $editor->company)
            ->join('user_relations', 'user_relations.id_company', '=', 'companies.id')
            ->join('users', 'user_relations.id_user', '=', 'users.id')
            ->where('user_relations.is_manager', 1)
            ->select('companies.*', 'users.id AS id_manager')
            ->first();
        $users = DB::table('users')
            ->join('user_relations', 'users.id', '=', 'user_relations.id_user')
            ->join('companies', 'companies.id', '=', 'user_relations.id_company')
            ->select('users.*', 'companies.nome_fantasia AS company', 'user_relations.is_manager AS is_manager')
            ->orderBy('type')
            ->get();
        $company_relations = DB::table('company_relations')
            ->where('id_contratante', $companies->id)
            ->first();
        $companies = DB::table('companies')
            ->join('company_relations', function($join) {
                $join->on('companies.id', '=', 'company_relations.id_contratada')->orOn('companies.id', '=', 'company_relations.id_contratante');
            })
            ->where('user_relations.is_manager', 1)
            ->join('user_relations', 'user_relations.id_company', '=', 'companies.id')
            ->join('users', 'user_relations.id_user', '=', 'users.id')
            ->select('companies.*', 'users.id AS id_manager')
            ->paginate(9)->unique();
        $companies = PaginationHelper::paginate($companies, 9);
        if (Auth::user()->type == 'Administrador'){
            $companies = DB::table('companies')
                ->join('user_relations', 'user_relations.id_company', '=', 'companies.id')
                ->join('users', 'user_relations.id_user', '=', 'users.id')
                ->where('user_relations.is_manager', 1)
                ->select('companies.*', 'users.id AS id_manager')
                ->paginate(9);
        }
        return view('companies.index', compact('companies', 'users', 'editor'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $editor = DB::table('users')
            ->where('users.id', Auth::user()->id)
            ->join('user_relations', 'users.id', '=', 'user_relations.id_user')
            ->join('companies', 'companies.id', '=', 'user_relations.id_company')
            ->select('users.*', 'companies.nome_fantasia AS company', 'user_relations.is_manager AS is_manager')
            ->first();
        if(!($editor->is_manager == 1 || Auth::user()->type == 'Administrador')){
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
        if(Auth::user()->type == 'Moderador'){
            $req['tipo'] = 'Contratada';
        }
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
            ->join('user_relations', 'user_relations.id_company', '=', 'companies.id')
            ->join('users', 'user_relations.id_user', '=', 'users.id')
            ->where('user_relations.is_manager', 1)
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
        // $user->roles()->sync($request->input('roles', []));
        return redirect()->route('companies.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Company $company)
    {
        $editor = DB::table('users')
            ->where('users.id', Auth::user()->id)
            ->join('user_relations', 'users.id', '=', 'user_relations.id_user')
            ->join('companies', 'companies.id', '=', 'user_relations.id_company')
            ->select('users.*', 'companies.nome_fantasia AS company', 'user_relations.is_manager AS is_manager')
            ->first();
        $users = User::all();
        $company = DB::table('companies')
            ->where('companies.id', $company->id)
            ->join('user_relations', 'user_relations.id_company', '=', 'companies.id')
            ->join('users', 'user_relations.id_user', '=', 'users.id')
            ->where('user_relations.is_manager', 1)
            ->select('companies.*', 'users.id AS id_manager')
            ->first();
        return view('companies.show', compact('company', 'users', 'editor'));
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
        $company = DB::table('companies')
            ->where('companies.id', $company->id)
            ->join('user_relations', 'user_relations.id_company', '=', 'companies.id')
            ->join('users', 'user_relations.id_user', '=', 'users.id')
            ->where('user_relations.is_manager', 1)
            ->select('companies.*', 'users.id AS id_manager')
            ->first();
        dump(isset($company));
        exit;
        return view('companies.edit', compact('company', 'users', 'editor'));
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCompanyRequest $request, Company $company)
    {
        $editor = DB::table('users')
            ->where('users.id', Auth::user()->id)
            ->where('user_relations.id_company', $company->id)
            ->join('user_relations', 'users.id', '=', 'user_relations.id_user')
            ->join('companies', 'companies.id', '=', 'user_relations.id_company')
            ->select('users.*', 'companies.nome_fantasia AS company', 'user_relations.is_manager AS is_manager', 'companies.id AS id_company')
            ->get();
        if(!(isset($editor->id))){ abort(403, 'Access denied');}
        if(!($editor->is_manager == 1 || Auth::user()->type == 'Administrador')){
            abort(403, 'Access denied');
        }
        $req = $request->validated();
        /* if(Auth::user()->type == 'Moderador'){
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

            if(isset($req['id_manager'])) {
                $manager = (int) $req['id_manager'];
            } else {
                throw ValidationException::withMessages(['erro' => 'Você não tem permissão para isso!']);
            }

            $relations = User_relation::findOrFail($relations->id);
        $relations->id_company = (int) $req['company'];
        
        $relations->update();
        unset($req['company']);
        }*/

        if(isset($req['id_manager'])) {
            $manager = (int) $req['id_manager'];
            unset($req['id_manager']);
            $new_manager = DB::table('user_relations')
                ->where('user_relations.id_user', $manager)
                ->where('user_relations.id_company', $company->id)
                ->join('users', 'users.id', '=', 'user_relations.id_user')
                ->select('user_relations.*')
                ->first();
            if(isset($new_manager)){
                $old_manager = DB::table('user_relations')
                    ->where('user_relations.id_company', $company->id)
                    ->where('user_relations.is_manager', 1)
                    ->join('users', 'users.id', '=', 'user_relations.id_user')
                    ->select('user_relations.*')
                    ->first();
                if($old_manager->id_user != $manager){
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
                ->where('user_relations.id_company', $company->id)
                ->join('users', 'users.id', '=', 'user_relations.id_user')
                ->select('user_relations.*')
                ->first();
            if(isset($new_manager)){
                $new_manager = User_relation::findOrFail($new_manager->id);
                $new_manager->is_manager = 1;
                $new_manager->update();
            }
        }
        $company->update($req);
        return redirect()->route('companies.index');
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
