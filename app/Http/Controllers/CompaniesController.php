<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreCompanyRequest;
use App\Models\Company;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
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
        
        // esse acesso das empresas deve ser só pra ADM? ou Mods?
        if (Auth::user()->type == 'Administrador' || Auth::user()->type == 'Moderador'){
            $companies = Company::orderBy('id')->paginate(9);
        }else{
            abort(403, 'Access denied');
        }
        $users = DB::table('users')
                ->join('user_relations', 'users.id', '=', 'user_relations.id_user')
                ->join('companies', 'companies.id', '=', 'user_relations.id_company')
                ->select('users.*', 'companies.nome_fantasia AS company', 'user_relations.is_manager AS is_manager')
                ->paginate(9);
        return view('companies.index', compact('companies', 'users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if(!(Auth::user()->type == 'Moderador' || Auth::user()->type == 'Administrador')){
            abort(403, 'Access denied');
        }
        // abort_if(Gate::denies('user_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $users = User::where('company', Auth::user()->company)->paginate(9);
        if (Auth::user()->type == 'Administrador'){
            $users = User::all();
        }
        return view('companies.create', compact('users'));
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
        // abort_if(Gate::denies('user_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        // $roles = Role::pluck('title', 'id');

        // $user->load('roles');
        $users = DB::table('users')
                ->join('user_relations', 'users.id', '=', 'user_relations.id_user')
                ->join('companies', 'companies.id', '=', 'user_relations.id_company')
                ->select('users.*', 'companies.nome_fantasia AS company', 'user_relations.is_manager AS is_manager')
                ->paginate(9);
        return view('companies.edit', compact('company', 'users'));
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
