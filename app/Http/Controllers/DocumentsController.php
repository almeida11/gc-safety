<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateDocumentRequest;
use App\Http\Requests\StoreDocumentRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Document;
use App\Models\Company;

class DocumentsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $documents = DB::table('documents')
            ->join('companies', 'companies.id', '=', 'documents.id_company')
            ->join('company_relations', function($join) {
                $join
                    ->on('companies.id', '=', 'company_relations.id_contratada')
                    ->orOn('companies.id', '=', 'company_relations.id_contratante');
            })
            ->select('documents.*', 'companies.nome_fantasia AS company', 'companies.tipo AS tipo')
            ->paginate(9);

        $editor = DB::table('users')
            ->where('users.id', Auth::user()->id)
            ->join('user_relations', 'users.id', '=', 'user_relations.id_user')
            ->join('companies', 'companies.id', '=', 'user_relations.id_company')
            ->select('users.*', 'companies.nome_fantasia AS company', 'companies.tipo AS tipo', 'user_relations.is_manager AS is_manager', 'companies.id as id_company')
            ->first();
        return view('documents.index', compact('editor', 'documents'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
        { $editor = DB::table('users')
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
        return view('documents.create', compact('companies', 'documents'));

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDocumentRequest $request)
    {
        //
        $req = $request->validated();

        $new_document = Document::create($req);

        return redirect()->route('documents.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Document $document) {
        $document = DB::table('documents')
            ->where('documents.id', $document->id)
            ->first();

        return view('documents.show', compact('document'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Document $document) {
        $document = DB::table('documents')
            ->where('documents.id', $document->id)
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
        return view('documents.edit', compact('document', 'companies'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDocumentRequest $request, Document $document)
    {
        $req = $request->validated();
        $document->update($req);
        return redirect()->route('documents.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Document $document) {
        /*$employees = Employee::all();

        foreach ($employees as $employee) {
            if ($employee->id_responsibility == $responsibility->id) throw ValidationException::withMessages(['cnpj' => 'O campo cnpj tem um formato inválido.!']);
        }

        Responsibility::where('id', $responsibility->id)->delete();*/
        
        return redirect()->route('documents.index');
    }
}
