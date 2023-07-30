<?php

namespace App\Http\Controllers;

use Illuminate\Validation\ValidationException;
use App\Http\Requests\UpdateEmployeeRequest;
use App\Http\Requests\StoreEmployeeRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Company_relation;
use App\Models\Responsibility;
use App\Models\Document_path;
use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Company;
use App\Models\Sector;


class EmployeesController extends Controller {
    public function index(Int $company_id) {
        $editor = DB::table('users')
            ->where('users.id', Auth::user()->id)
            ->join('user_relations', 'users.id', '=', 'user_relations.id_user')
            ->join('companies', 'companies.id', '=', 'user_relations.id_company')
            ->select('users.*', 'companies.nome_fantasia AS company', 'companies.tipo AS tipo', 'user_relations.is_manager AS is_manager', 'companies.id as id_company')
            ->first();

            
        $employees = DB::table('employees')
            ->join('companies', 'companies.id', '=', 'employees.id_company')
            ->where('companies.id', $company_id)
            ->join('responsibilities', 'responsibilities.id', '=', 'employees.id_responsibility')
            ->join('sectors', 'sectors.id', '=', 'employees.id_sector')
            ->select('employees.*', 'companies.nome_fantasia AS company', 'responsibilities.name AS responsibility', 'sectors.name AS sector')
            ->paginate(9);

        $sectors = DB::table('employees')
            ->get();
        
        $responsibilities = DB::table('employees')
            ->get();

        return view('employees.index', compact('employees', 'sectors', 'responsibilities', 'company_id'));
    }
    
    public function create(Int $company_id) {
        $editor = DB::table('users')
            ->where('users.id', Auth::user()->id)
            ->join('user_relations', 'users.id', '=', 'user_relations.id_user')
            ->join('companies', 'companies.id', '=', 'user_relations.id_company')
            ->select('users.*', 'companies.nome_fantasia AS company', 'companies.tipo AS tipo', 'user_relations.is_manager AS is_manager', 'companies.id as id_company')
            ->first();
        
        $companies = DB::table('companies')
            ->where('company_relations.id_contratante', $editor->id_company)
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
            ->get()->unique();

        $sectors = DB::table('sectors')
            ->join('companies', 'companies.id', '=', 'sectors.id_company')
            ->where('companies.id', $company_id)
            ->join('company_relations', function($join) {
                $join
                    ->on('companies.id', '=', 'company_relations.id_contratada')
                    ->orOn('companies.id', '=', 'company_relations.id_contratante');
            })
            ->select('sectors.*', 'companies.nome_fantasia AS company', 'companies.tipo AS tipo')
            ->get();
        
        $responsibilities = DB::table('responsibilities')
            ->join('companies', 'companies.id', '=', 'responsibilities.id_company')
            ->where('companies.id', $company_id)
            ->join('company_relations', function($join) {
                $join
                    ->on('companies.id', '=', 'company_relations.id_contratada')
                    ->orOn('companies.id', '=', 'company_relations.id_contratante');
            })
            ->select('responsibilities.*', 'companies.nome_fantasia AS company', 'companies.tipo AS tipo')
            ->get();
    
        return view('employees.create', compact('companies', 'sectors', 'responsibilities', 'company_id'));
    }
    
    public function store(Int $company_id, StoreEmployeeRequest $request) {
        $editor = DB::table('users')
            ->where('users.id', Auth::user()->id)
            ->join('user_relations', 'users.id', '=', 'user_relations.id_user')
            ->join('companies', 'companies.id', '=', 'user_relations.id_company')
            ->select('users.*', 'companies.nome_fantasia AS company', 'companies.tipo AS tipo', 'user_relations.is_manager AS is_manager', 'companies.id as id_company')
            ->first();

        $req = $request->validated();

        // if($editor->id_company != (Int) $req['id_company']) abort(403, 'Access denied');

        $new_employee = Employee::create($req);

        return redirect()->route('employees.index', $company_id);
    }
    
    public function show(Int $company_id, Employee $employee) {
        $editor = DB::table('users')
            ->where('users.id', Auth::user()->id)
            ->join('user_relations', 'users.id', '=', 'user_relations.id_user')
            ->join('companies', 'companies.id', '=', 'user_relations.id_company')
            ->select('users.*', 'companies.nome_fantasia AS company', 'companies.tipo AS tipo', 'user_relations.is_manager AS is_manager', 'companies.id as id_company')
            ->first();

        $employee = DB::table('employees')
            ->where('employees.id', $employee->id)
            ->join('companies', 'companies.id', '=', 'employees.id_company')
            ->join('responsibilities', 'responsibilities.id', '=', 'employees.id_responsibility')
            ->join('sectors', 'sectors.id', '=', 'employees.id_sector')
            ->select('employees.*', 'companies.nome_fantasia AS company', 'responsibilities.name AS responsibility', 'sectors.name AS sector')
            ->first();

        $responsibilities = DB::table('responsibilities')
            ->join('companies', 'companies.id', '=', 'responsibilities.id_company')
            ->where('companies.id', $company_id)
            ->join('company_relations', function($join) {
                $join
                    ->on('companies.id', '=', 'company_relations.id_contratada')
                    ->orOn('companies.id', '=', 'company_relations.id_contratante');
            })
            ->select('responsibilities.*', 'companies.nome_fantasia AS company', 'companies.tipo AS tipo')
            ->paginate(9);
        if($editor->company != $employee->company) abort(403, 'Access denied');
        $documents = DB::table('documents')
            ->join('companies', 'companies.id', '=', 'documents.id_company')
            ->join('company_relations', function($join) {
                $join
                    ->on('companies.id', '=', 'company_relations.id_contratada')
                    ->orOn('companies.id', '=', 'company_relations.id_contratante');
            })
            ->select('documents.*', 'companies.nome_fantasia AS company', 'companies.tipo AS tipo')
            ->get();

        $document_paths = DB::table('document_paths')
            ->where('id_employee', $employee->id)
            ->get();
            
        return view('employees.show', compact('employee', 'documents', 'document_paths', 'company_id', 'responsibilities'));
    }

    public function edit(Int $company_id, Employee $employee) {
        $editor = DB::table('users')
            ->where('users.id', Auth::user()->id)
            ->join('user_relations', 'users.id', '=', 'user_relations.id_user')
            ->join('companies', 'companies.id', '=', 'user_relations.id_company')
            ->select('users.*', 'companies.nome_fantasia AS company', 'companies.tipo AS tipo', 'user_relations.is_manager AS is_manager', 'companies.id as id_company')
            ->first();
        
        $employee = DB::table('employees')
            ->where('employees.id', $employee->id)
            ->join('companies', 'companies.id', '=', 'employees.id_company')
            ->join('responsibilities', 'responsibilities.id', '=', 'employees.id_responsibility')
            ->join('sectors', 'sectors.id', '=', 'employees.id_sector')
            ->select('employees.*', 'companies.nome_fantasia AS company', 'responsibilities.name AS responsibility', 'sectors.name AS sector')
            ->first();

        if($editor->company != $employee->company) abort(403, 'Access denied');

        $documents = DB::table('documents')
            ->join('companies', 'companies.id', '=', 'documents.id_company')
            ->join('company_relations', function($join) {
                $join
                    ->on('companies.id', '=', 'company_relations.id_contratada')
                    ->orOn('companies.id', '=', 'company_relations.id_contratante');
            })
            ->select('documents.*', 'companies.nome_fantasia AS company', 'companies.tipo AS tipo')
            ->get();
        
        $document_paths = DB::table('document_paths')
            ->where('id_employee', $employee->id)
            ->get();
        
        
        $companies = DB::table('companies')
            ->where('company_relations.id_contratante', $editor->id_company)
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
            ->get()->unique();

        $sectors = DB::table('sectors')
            ->join('companies', 'companies.id', '=', 'sectors.id_company')
            ->where('companies.id', $company_id)
            ->join('company_relations', function($join) {
                $join
                    ->on('companies.id', '=', 'company_relations.id_contratada')
                    ->orOn('companies.id', '=', 'company_relations.id_contratante');
            })
            ->select('sectors.*', 'companies.nome_fantasia AS company', 'companies.tipo AS tipo')
            ->get();
        
        $responsibilities = DB::table('responsibilities')
            ->join('companies', 'companies.id', '=', 'responsibilities.id_company')
            ->where('companies.id', $company_id)
            ->join('company_relations', function($join) {
                $join
                    ->on('companies.id', '=', 'company_relations.id_contratada')
                    ->orOn('companies.id', '=', 'company_relations.id_contratante');
            })
            ->select('responsibilities.*', 'companies.nome_fantasia AS company', 'companies.tipo AS tipo')
            ->get();

        
        return view('employees.edit', compact('employee', 'documents', 'companies', 'sectors', 'responsibilities', 'document_paths', 'company_id'));
    }
    
    public function update(Int $company_id, UpdateEmployeeRequest $request, Employee $employee) {
        $editor = DB::table('users')
            ->where('users.id', Auth::user()->id)
            ->join('user_relations', 'users.id', '=', 'user_relations.id_user')
            ->join('companies', 'companies.id', '=', 'user_relations.id_company')
            ->select('users.*', 'companies.nome_fantasia AS company', 'companies.tipo AS tipo', 'user_relations.is_manager AS is_manager', 'companies.id as id_company')
            ->first();
        // if($editor->id_company != $employee->id_company) abort(403, 'Access denied');
        if($employee->documents){
            foreach (json_decode($employee->documents) as $document) {
                if($request->{$document}) {
                    $old_document = DB::table('document_paths')
                        ->where('id_employee', $employee->id)
                        ->where('type', $document)
                        ->first();

                    $company = DB::table('companies')
                        ->where('companies.id', $employee->id_company)
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

                    $extension = $request->{$document}->getClientOriginalExtension();
                    if($extension != 'pdf') throw ValidationException::withMessages(['document'.$document => 'Você deve enviar somente arquivos do tipo pdf.']);

                    $path = 'documentos/'.$company->nome_fantasia.'/'.$employee->name;
                    $path = preg_replace('/[ -]+/' , '_' , strtolower( preg_replace("[^a-zA-Z0-9-]", "-", strtr(utf8_decode(trim($path)), utf8_decode("áàãâéêíóôõúüñçÁÀÃÂÉÊÍÓÔÕÚÜÑÇ"), "aaaaeeiooouuncAAAAEEIOOOUUNC-")) ));

                    $document_name = $document . '_' . $employee->id. "_" . $employee->name . ".{$extension}";
                    $document_name = preg_replace('/[ -]+/' , '_' , strtolower( preg_replace("[^a-zA-Z0-9-]", "-", strtr(utf8_decode(trim($document_name)), utf8_decode("áàãâéêíóôõúüñçÁÀÃÂÉÊÍÓÔÕÚÜÑÇ"), "aaaaeeiooouuncAAAAEEIOOOUUNC-")) ));
                    
                    if($old_document){
                        if (Storage::exists($old_document->name)) {
                            Storage::delete($old_document->name);
                        }
                        $request->{$document}->storeAs($path, $document_name);

                        $old_document = Document_path::findOrFail($old_document->id);
                        
                        $old_document->name = $document_name;
                        $old_document->path = $path;

                        $old_document->update();
                    } else {
                        $request->{$document}->storeAs($path, $document_name);
                        $document_path_model = array(
                            'path' => $path,
                            'name' => $document_name,
                            'type' => $document,
                            'id_employee' => $employee->id,
                        );
                        $document_path = Document_path::create($document_path_model);
                    }
                }
            }
        }
        // $responsibilities = DB::table('responsibilities')
        //     ->join('companies', 'companies.id', '=', 'responsibilities.id_company')
        //     ->where('responsibilities.id', $employee->id_responsibility)
        //     ->where('companies.id', $company_id)
        //     ->join('company_relations', function($join) {
        //         $join
        //             ->on('companies.id', '=', 'company_relations.id_contratada')
        //             ->orOn('companies.id', '=', 'company_relations.id_contratante');
        //     })
        //     ->select('responsibilities.*', 'companies.nome_fantasia AS company', 'companies.tipo AS tipo')
        //     ->first();
            
        $req = $request->validated();
        //$employee->documents = $responsibilities->documents;
        $employee->update($req);
        return redirect()->route('employees.edit', [$company_id, $employee->id]);
    }
    
    public function destroy(Int $company_id, Employee $employee) {
        $editor = DB::table('users')
            ->where('users.id', Auth::user()->id)
            ->join('user_relations', 'users.id', '=', 'user_relations.id_user')
            ->join('companies', 'companies.id', '=', 'user_relations.id_company')
            ->select('users.*', 'companies.nome_fantasia AS company', 'companies.tipo AS tipo', 'user_relations.is_manager AS is_manager', 'companies.id as id_company')
            ->first();
            
        // if($editor->id_company != $employee->id_company) abort(403, 'Access denied');

        $employee->active = 0;
        
        $employee->save();
        
        return redirect()->route('employees.index', $company_id);
    }

    public function editdoc(Int $company_id, Int $employee_id, Request $request) { // document_manager
        $employee = Employee::findOrFail($employee_id);

        foreach (json_decode($employee->documents) as $old_type_document) {
            $old_document = DB::table('document_paths')
                ->where('id_employee', $employee_id)
                ->where('type', $old_type_document)
                ->first();
            if($old_document){
                if(in_array($old_document->type, $request->documents)) {
                    throw ValidationException::withMessages(['document_manager' => 'Já existe documento enviado, favor excluir documento primeiro!']);
                }
            }
        }
        dd($this);
        
        $employee->documents = $request->documents;
        $employee->save();
        return redirect()->route('employees.edit', [$company_id, $employee_id]);
    }
}