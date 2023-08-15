<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateDocumentRequest;
use App\Http\Requests\StoreDocumentRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Document;
use App\Models\Company;

class DocumentsController extends Controller {
    public function index(Int $company_id) {

        $busca = isset($_GET['query-documents']) ? $_GET['query-documents'] : '';

        $documents = DB::table('documents')
            ->join('companies', 'companies.id', '=', 'documents.id_company')
            ->where('companies.id', $company_id)
            ->join('company_relations', function($join) {
                $join
                    ->on('companies.id', '=', 'company_relations.id_contratada')
                    ->orOn('companies.id', '=', 'company_relations.id_contratante');
            })
            ->where(function ($query) use ($busca) {
                $query->where('documents.id', 'LIKE', '%' . $busca . '%')
                ->orWhere('documents.name', 'LIKE', '%' . $busca . '%')
                ->orWhere('companies.name', 'LIKE', '%' . $busca . '%');
            })
            ->select('documents.*', 'companies.name AS company', 'companies.tipo AS tipo')
            ->paginate(9);

        $editor = DB::table('users')
            ->where('users.id', Auth::user()->id)
            ->join('user_relations', 'users.id', '=', 'user_relations.id_user')
            ->join('companies', 'companies.id', '=', 'user_relations.id_company')
            ->select('users.*', 'companies.name AS company', 'companies.tipo AS tipo', 'user_relations.is_manager AS is_manager', 'companies.id as id_company')
            ->first();
        return view('documents.index', compact('editor', 'documents', 'company_id', 'busca'));
    }

    public function create(Int $company_id) {
        $editor = DB::table('users')
            ->where('users.id', Auth::user()->id)
            ->join('user_relations', 'users.id', '=', 'user_relations.id_user')
            ->join('companies', 'companies.id', '=', 'user_relations.id_company')
            ->select('users.*', 'companies.name AS company', 'companies.tipo AS tipo', 'user_relations.is_manager AS is_manager', 'companies.id as id_company')
            ->first();
        
        $documents = DB::table('documents')
            ->join('companies', 'companies.id', '=', 'documents.id_company')
            ->where('companies.id', $company_id)
            ->join('company_relations', function($join) {
                $join
                    ->on('companies.id', '=', 'company_relations.id_contratada')
                    ->orOn('companies.id', '=', 'company_relations.id_contratante');
            })
            ->select('documents.*', 'companies.name AS company', 'companies.tipo AS tipo')
            ->paginate(9);

        $companies = DB::table('companies')
            ->where('companies.id', $company_id)
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
        return view('documents.create', compact('companies', 'documents', 'company_id'));

    }
    
    public function store(Int $company_id, StoreDocumentRequest $request) {
        //
        $req = $request->validated();

        $new_document = Document::create($req);

        return redirect()->route('documents.index', $company_id);
    }
    
    public function show(Int $company_id, Document $document) {
        $document = DB::table('documents')
            ->where('documents.id', $document->id)
            ->first();

        return view('documents.show', compact('document', 'company_id'));
    }
    
    public function edit(Int $company_id, Document $document) {
        $document = DB::table('documents')
            ->where('documents.id', $document->id)
            ->first();
        $editor = DB::table('users')
        ->where('users.id', Auth::user()->id)
        ->join('user_relations', 'users.id', '=', 'user_relations.id_user')
        ->join('companies', 'companies.id', '=', 'user_relations.id_company')
        ->select('users.*', 'companies.name AS company', 'companies.tipo AS tipo', 'user_relations.is_manager AS is_manager', 'companies.id as id_company')
        ->first();
        
        $companies = DB::table('companies')
            ->where('companies.id', $company_id)
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
        return view('documents.edit', compact('document', 'companies', 'company_id'));
    }
    
    public function update(Int $company_id, UpdateDocumentRequest $request, Document $document) {
        $req = $request->validated();
        $document->update($req);
        return redirect()->route('documents.index', $company_id);
    }
    
    public function destroy(Int $company_id, Document $document) {
        /*$employees = Employee::all();

        foreach ($employees as $employee) {
            if ($employee->id_responsibility == $responsibility->id) throw ValidationException::withMessages(['cnpj' => 'O campo cnpj tem um formato inválido.!']);
        }

        Responsibility::where('id', $responsibility->id)->delete();*/
        
        return redirect()->route('documents.index', $company_id);
    }
}
