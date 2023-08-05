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

        $documents = DB::table('document_paths')
            ->get()->unique();

        $employees_doc_status = array();

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
                        foreach ($employees_doc_status as $value) {
                            if($value['id'] == $employee->id) {
                                $check_test = false;
                            }
                        }
                        if($check_test) {
                            array_push($employees_doc_status, array ( 'id' => $employee->id, 'status' => false ));
                        }
                    }
                }
            }
        }

        return view('employees.index', compact('employees', 'sectors', 'responsibilities', 'company_id', 'employees_doc_status', 'editor'));
    }
    
    public function create(Int $company_id) {
        $editor = DB::table('users')
            ->where('users.id', Auth::user()->id)
            ->join('user_relations', 'users.id', '=', 'user_relations.id_user')
            ->join('companies', 'companies.id', '=', 'user_relations.id_company')
            ->select('users.*', 'companies.nome_fantasia AS company', 'companies.tipo AS tipo', 'user_relations.is_manager AS is_manager', 'companies.id as id_company')
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

        // if($editor->company != $employee->company) abort(403, 'Access denied');

        $documents = DB::table('documents')
            ->join('companies', 'companies.id', '=', 'documents.id_company')
            ->where('companies.id', $company_id)
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

        
        return view('employees.edit', compact('employee', 'documents', 'companies', 'sectors', 'responsibilities', 'document_paths', 'company_id', 'editor'));
    }
    
    public function update(Int $company_id, UpdateEmployeeRequest $request, Employee $employee) {
        $editor = DB::table('users')
            ->where('users.id', Auth::user()->id)
            ->join('user_relations', 'users.id', '=', 'user_relations.id_user')
            ->join('companies', 'companies.id', '=', 'user_relations.id_company')
            ->select('users.*', 'companies.nome_fantasia AS company', 'companies.tipo AS tipo', 'user_relations.is_manager AS is_manager', 'companies.id as id_company')
            ->first();
        
        $req = $request->validated();
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

    public function editdoc(Int $company_id, Int $employee_id, Request $request) { // Tipos de Documentos
        $employee = Employee::findOrFail($employee_id);

        if(json_decode($employee->documents)){
            foreach (json_decode($employee->documents) as $old_type_document) {
                $old_document = DB::table('document_paths')
                    ->where('id_employee', $employee_id)
                    ->where('type', $old_type_document)
                    ->first();
                if($old_document){
                    if(!(in_array($old_document->type, $request->documents))) {
                        throw ValidationException::withMessages(['document_manager' => 'Já existe documento enviado, favor excluir documento primeiro!']);
                    }
                }
            }
        }

        $employee->documents = $request->documents;
        $employee->save();
        
        return redirect()->route('employees.edit', [$company_id, $employee_id]);
    }

    public function updatedoc(Int $company_id, Int $employee_id, Request $request) { // Envio de documentos
        $employee = Employee::findOrFail($employee_id);
        // if($editor->id_company != $employee->id_company) abort(403, 'Access denied');
        if($employee->documents){
            foreach (json_decode($employee->documents) as $document) {
                $old_document = DB::table('document_paths')
                    ->where('id_employee', $employee->id)
                    ->where('type', $document)
                    ->first();
                if($request->{$document}) {
                    if(!(isset($request->due_date))) throw ValidationException::withMessages(['document_uploader' => 'Você deve enviar uma data de vencimento válida.', 'document_uploader_type'  => $document]);

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
                    if($extension != 'pdf') throw ValidationException::withMessages(['document_uploader' => 'Você deve enviar somente arquivos do tipo pdf.', 'document_uploader_type'  => $document]);

                    $path = 'documents/'.$company->nome_fantasia.'/'.$employee->name;
                    $path = preg_replace('/[ -]+/' , '_' , strtolower( preg_replace("[^a-zA-Z0-9-]", "-", strtr(utf8_decode(trim($path)), utf8_decode("áàãâéêíóôõúüñçÁÀÃÂÉÊÍÓÔÕÚÜÑÇ"), "aaaaeeiooouuncAAAAEEIOOOUUNC-")) ));

                    $document_name = $document . '_' . $employee->id. "_" . $employee->name . ".{$extension}";
                    $document_name = preg_replace('/[ -]+/' , '_' , strtolower( preg_replace("[^a-zA-Z0-9-]", "-", strtr(utf8_decode(trim($document_name)), utf8_decode("áàãâéêíóôõúüñçÁÀÃÂÉÊÍÓÔÕÚÜÑÇ"), "aaaaeeiooouuncAAAAEEIOOOUUNC-")) ));
                    
                    if($old_document){
                        if (Storage::exists($old_document->path . "/" . $old_document->name)) {
                            $path_to_old = $old_document->path . "/old/";
                            $name_to_old = (String) (count($files = Storage::files($old_document->path . "/old")) + 1). "_" . $old_document->name;
                            Storage::move($old_document->path . "/" . $old_document->name, $path_to_old . $name_to_old);
                            $document_path_model = array(
                                'path' => $path_to_old,
                                'due_date' => $old_document->due_date,
                                'name' => $name_to_old,
                                'type' => $old_document->type,
                                'id_employee' => $old_document->id_employee,
                            );
                            $document_path = Document_path::create($document_path_model);
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
                            'due_date' => $request->due_date,
                            'name' => $document_name,
                            'type' => $document,
                            'id_employee' => $employee->id,
                        );
                        $document_path = Document_path::create($document_path_model);
                    }
                } else {
                    if($old_document) {
                        if($old_document->type == $request->modal_type) {
                            if($request->due_date) {
                                $old_document = Document_path::findOrFail($old_document->id);
                                $old_document->due_date = $request->due_date;
                                $old_document->update();
                            }
                        }
                    } else {
                        throw ValidationException::withMessages(['document_uploader' => 'Você deve enviar um arquivo!', 'document_uploader_type'  => $request->modal_type]);
                    }
                }
            }
        }
        return redirect()->route('employees.edit', [$company_id, $employee_id]);
    }
}