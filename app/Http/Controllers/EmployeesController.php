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

        $busca = isset($_GET['query-employees']) ? $_GET['query-employees'] : '';
        $orderby = isset($_GET['order-companie']) ? $_GET['order-companie'] : 'id';
        $method = isset($_GET['method-companie']) ? $_GET['method-companie'] : 'asc';

        $editor = DB::table('users')
            ->where('users.id', Auth::user()->id)
            ->leftjoin('user_relations', 'users.id', '=', 'user_relations.id_user')
            ->leftjoin('companies', 'companies.id', '=', 'user_relations.id_company')
            ->select('users.*', 'companies.name AS company', 'companies.tipo AS tipo', 'user_relations.is_manager AS is_manager', 'companies.id as id_company')
            ->first();
        if(($editor->tipo == 'Contratada') && !($editor->id_company == $company_id)) abort(404, 'Access denied');

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
            
            if(!(isset($contratante))) abort(404, 'Access denied');
        
            if(!($company->id_contratante == $editor->id_company) && !($company->id == $editor->id_company)) abort(404, 'Access denied');
        } else {
            if(!($editor->company == $company->name)) abort(404, 'Access denied');
        }
            
        $employees = DB::table('employees')
            ->leftjoin('companies', 'companies.id', '=', 'employees.id_company')
            ->where('companies.id', $company_id)
            ->leftjoin('responsibilities', 'responsibilities.id', '=', 'employees.id_responsibility')
            ->leftjoin('sectors', 'sectors.id', '=', 'employees.id_sector')
            ->where(function ($query) use ($busca) {
                $query->where('employees.id', 'LIKE', '%' . $busca . '%')
                ->orWhere('employees.name', 'LIKE', '%' . $busca . '%')
                ->orWhere('responsibilities.name', 'LIKE', '%' . $busca . '%')
                ->orWhere('sectors.name', 'LIKE', '%' . $busca . '%');
            })
            ->select('employees.*', 'companies.name AS company', 'responsibilities.name AS responsibility', 'sectors.name AS sector')
            ->orderBy($orderby, $method)
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
                            if(($document_name->id_employee == $employee->id && $document_name->type == $document && $document_name->status == 'Aprovado')) {
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
        return view('employees.index', compact('employees', 'sectors', 'responsibilities', 'company_id', 'employees_doc_status', 'editor', 'busca', 'orderby', 'method'));
    }
    
    public function create(Int $company_id) {
        $editor = DB::table('users')
            ->where('users.id', Auth::user()->id)
            ->leftjoin('user_relations', 'users.id', '=', 'user_relations.id_user')
            ->leftjoin('companies', 'companies.id', '=', 'user_relations.id_company')
            ->select('users.*', 'companies.name AS company', 'companies.tipo AS tipo', 'user_relations.is_manager AS is_manager', 'companies.id as id_company')
            ->first();
            
        if(!($editor->type == 'Administrador' || $editor->type == 'Cliente' || $editor->type == 'Prestador')) abort(404, 'Access denied');
        
        $companies = DB::table('companies')
            ->where('companies.id', $company_id)
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
            ->select('companies.*', 'users.id AS id_manager', 'company_relations.id_contratante')
            ->get()->unique();

        $sectors = DB::table('sectors')
            ->leftjoin('companies', 'companies.id', '=', 'sectors.id_company')
            ->where('companies.id', $company_id)
            ->leftjoin('company_relations', function($join) {
                $join
                    ->on('companies.id', '=', 'company_relations.id_contratada')
                    ->orOn('companies.id', '=', 'company_relations.id_contratante');
            })
            ->select('sectors.*', 'companies.name AS company', 'companies.tipo AS tipo')
            ->get();
        
        $responsibilities = DB::table('responsibilities')
            ->leftjoin('companies', 'companies.id', '=', 'responsibilities.id_company')
            ->where('companies.id', $company_id)
            ->leftjoin('company_relations', function($join) {
                $join
                    ->on('companies.id', '=', 'company_relations.id_contratada')
                    ->orOn('companies.id', '=', 'company_relations.id_contratante');
            })
            ->select('responsibilities.*', 'companies.name AS company', 'companies.tipo AS tipo')
            ->get();
    
        return view('employees.create', compact('companies', 'sectors', 'responsibilities', 'company_id'));
    }
    
    public function store(Int $company_id, StoreEmployeeRequest $request) {
        $editor = DB::table('users')
            ->where('users.id', Auth::user()->id)
            ->leftjoin('user_relations', 'users.id', '=', 'user_relations.id_user')
            ->leftjoin('companies', 'companies.id', '=', 'user_relations.id_company')
            ->select('users.*', 'companies.name AS company', 'companies.tipo AS tipo', 'user_relations.is_manager AS is_manager', 'companies.id as id_company')
            ->first();
        if(!($editor->type == 'Administrador' || $editor->type == 'Cliente' || $editor->type == 'Prestador')) abort(404, 'Access denied');

        $req = $request->validated();

        // if($editor->id_company != (Int) $req['id_company']) abort(403, 'Access denied');

        $new_employee = Employee::create($req);

        return redirect()->route('employees.index', $company_id);
    }
    
    public function show(Int $company_id, Employee $employee) {
        $editor = DB::table('users')
            ->where('users.id', Auth::user()->id)
            ->leftjoin('user_relations', 'users.id', '=', 'user_relations.id_user')
            ->leftjoin('companies', 'companies.id', '=', 'user_relations.id_company')
            ->select('users.*', 'companies.name AS company', 'companies.tipo AS tipo', 'user_relations.is_manager AS is_manager', 'companies.id as id_company')
            ->first();
        if(($editor->tipo == 'Contratada') && !($editor->id_company == $company_id)) abort(404, 'Access denied');

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
            
            if(!(isset($contratante))) abort(404, 'Access denied');
        
            if(!($company->id_contratante == $editor->id_company) && !($company->id == $editor->id_company)) abort(404, 'Access denied');
        } else {
            if(!($editor->company == $company->name)) abort(404, 'Access denied');
        }
        $employee = DB::table('employees')
            ->where('employees.id', $employee->id)
            ->leftjoin('companies', 'companies.id', '=', 'employees.id_company')
            ->where('companies.id', $company_id)
            ->leftjoin('responsibilities', 'responsibilities.id', '=', 'employees.id_responsibility')
            ->leftjoin('sectors', 'sectors.id', '=', 'employees.id_sector')
            ->select('employees.*', 'companies.name AS company', 'responsibilities.name AS responsibility', 'sectors.name AS sector')
            ->first();

        if(!($employee)) abort(404, 'Access denied');

        $responsibilities = DB::table('responsibilities')
            ->leftjoin('companies', 'companies.id', '=', 'responsibilities.id_company')
            ->where('companies.id', $company_id)
            ->leftjoin('company_relations', function($join) {
                $join
                    ->on('companies.id', '=', 'company_relations.id_contratada')
                    ->orOn('companies.id', '=', 'company_relations.id_contratante');
            })
            ->select('responsibilities.*', 'companies.name AS company', 'companies.tipo AS tipo')
            ->paginate(9);

        $documents = DB::table('documents')
            ->leftjoin('companies', 'companies.id', '=', 'documents.id_company')
            ->leftjoin('company_relations', function($join) {
                $join
                    ->on('companies.id', '=', 'company_relations.id_contratada')
                    ->orOn('companies.id', '=', 'company_relations.id_contratante');
            })
            ->select('documents.*', 'companies.name AS company', 'companies.tipo AS tipo')
            ->get();

        $document_paths = DB::table('document_paths')
            ->where('id_employee', $employee->id)
            ->get();
            
        return view('employees.show', compact('employee', 'documents', 'document_paths', 'company_id', 'responsibilities'));
    }

    public function edit(Int $company_id, Employee $employee) {
        $editor = DB::table('users')
            ->where('users.id', Auth::user()->id)
            ->leftjoin('user_relations', 'users.id', '=', 'user_relations.id_user')
            ->leftjoin('companies', 'companies.id', '=', 'user_relations.id_company')
            ->select('users.*', 'companies.name AS company', 'companies.tipo AS tipo', 'user_relations.is_manager AS is_manager', 'companies.id as id_company')
            ->first();
        if(!($editor->type == 'Administrador' || $editor->type == 'Cliente' || $editor->type == 'Prestador')) abort(404, 'Access denied');
        
        $employee = DB::table('employees')
            ->where('employees.id', $employee->id)
            ->leftjoin('companies', 'companies.id', '=', 'employees.id_company')
            ->leftjoin('responsibilities', 'responsibilities.id', '=', 'employees.id_responsibility')
            ->leftjoin('sectors', 'sectors.id', '=', 'employees.id_sector')
            ->select('employees.*', 'companies.name AS company', 'responsibilities.name AS responsibility', 'sectors.name AS sector')
            ->first();

        // if($editor->company != $employee->company) abort(403, 'Access denied');

        $documents = DB::table('documents')
            ->leftjoin('companies', 'companies.id', '=', 'documents.id_company')
            ->where('companies.id', $company_id)
            ->leftjoin('company_relations', function($join) {
                $join
                    ->on('companies.id', '=', 'company_relations.id_contratada')
                    ->orOn('companies.id', '=', 'company_relations.id_contratante');
            })
            ->select('documents.*', 'companies.name AS company', 'companies.tipo AS tipo')
            ->get();
        
        $document_paths = DB::table('document_paths')
            ->where('id_employee', $employee->id)
            ->orderBy('created_at')
            ->get();

        $companies = DB::table('companies')
            ->where('companies.id', $company_id)
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
            ->select('companies.*', 'users.id AS id_manager', 'company_relations.id_contratante')
            ->get()->unique();

        $sectors = DB::table('sectors')
            ->leftjoin('companies', 'companies.id', '=', 'sectors.id_company')
            ->where('companies.id', $company_id)
            ->leftjoin('company_relations', function($join) {
                $join
                    ->on('companies.id', '=', 'company_relations.id_contratada')
                    ->orOn('companies.id', '=', 'company_relations.id_contratante');
            })
            ->select('sectors.*', 'companies.name AS company', 'companies.tipo AS tipo')
            ->get();
        
        $responsibilities = DB::table('responsibilities')
            ->leftjoin('companies', 'companies.id', '=', 'responsibilities.id_company')
            ->where('companies.id', $company_id)
            ->leftjoin('company_relations', function($join) {
                $join
                    ->on('companies.id', '=', 'company_relations.id_contratada')
                    ->orOn('companies.id', '=', 'company_relations.id_contratante');
            })
            ->select('responsibilities.*', 'companies.name AS company', 'companies.tipo AS tipo')
            ->get();

        
        return view('employees.edit', compact('employee', 'documents', 'companies', 'sectors', 'responsibilities', 'document_paths', 'company_id', 'editor'));
    }
    
    public function update(Int $company_id, UpdateEmployeeRequest $request, Employee $employee) {
        $editor = DB::table('users')
            ->where('users.id', Auth::user()->id)
            ->leftjoin('user_relations', 'users.id', '=', 'user_relations.id_user')
            ->leftjoin('companies', 'companies.id', '=', 'user_relations.id_company')
            ->select('users.*', 'companies.name AS company', 'companies.tipo AS tipo', 'user_relations.is_manager AS is_manager', 'companies.id as id_company')
            ->first();
        if(!($editor->type == 'Administrador' || $editor->type == 'Cliente' || $editor->type == 'Prestador')) abort(404, 'Access denied');

        $employee_check = DB::table('employees')
            ->where('employees.id', $employee->id)
            ->leftjoin('companies', 'companies.id', '=', 'employees.id_company')
            ->leftjoin('responsibilities', 'responsibilities.id', '=', 'employees.id_responsibility')
            ->leftjoin('sectors', 'sectors.id', '=', 'employees.id_sector')
            ->select('employees.*', 'companies.name AS company', 'responsibilities.name AS responsibility', 'sectors.name AS sector')
            ->first();

        if($request->employee_photo_path) {
            if(explode("/", $request->employee_photo_path->getClientmimeType())[0] != 'image') {
                throw ValidationException::withMessages(['foto' => 'Você deve enviar somente arquivos de imagem.']);
            }

            $extension = $request->employee_photo_path->getClientOriginalExtension();

            $path = 'documents/'.$employee_check->company.'/'.$employee_check->name.'/';
            $path = preg_replace('/[ -]+/' , '_' , strtolower( preg_replace("[^a-zA-Z0-9-]", "-", strtr(utf8_decode(trim($path)), utf8_decode("áàãâéêíóôõúüñçÁÀÃÂÉÊÍÓÔÕÚÜÑÇ"), "aaaaeeiooouuncAAAAEEIOOOUUNC-")) ));

            $document_name = 'FOTO_' . $employee_check->id. "_" . $employee_check->name . ".{$extension}";
            $document_name = preg_replace('/[ -]+/' , '_' , strtolower( preg_replace("[^a-zA-Z0-9-]", "-", strtr(utf8_decode(trim($document_name)), utf8_decode("áàãâéêíóôõúüñçÁÀÃÂÉÊÍÓÔÕÚÜÑÇ"), "aaaaeeiooouuncAAAAEEIOOOUUNC-")) ));

            $request->employee_photo_path->storeAs($path, $document_name);
        }

        $req = $request->validated();
        
        if($request->deleteProfilePhoto == 'deleteProfilePhoto') {
            Storage::disk('public')->delete($employee->employee_photo_path);
            $req['employee_photo_path'] = null;
        }

        if($request->employee_photo_path) {
            $req['employee_photo_path'] = $path . $document_name;
        }

        $employee->update($req);

        return redirect()->route('employees.edit', [$company_id, $employee->id]);
    }
    
    public function destroy(Int $company_id, Employee $employee) {
        $editor = DB::table('users')
            ->where('users.id', Auth::user()->id)
            ->leftjoin('user_relations', 'users.id', '=', 'user_relations.id_user')
            ->leftjoin('companies', 'companies.id', '=', 'user_relations.id_company')
            ->select('users.*', 'companies.name AS company', 'companies.tipo AS tipo', 'user_relations.is_manager AS is_manager', 'companies.id as id_company')
            ->first();
        if(!($editor->type == 'Administrador' || $editor->type == 'Cliente' || $editor->type == 'Prestador')) abort(404, 'Access denied');
            
        // if($editor->id_company != $employee->id_company) abort(403, 'Access denied');

        $employee->active = 0;
        
        $employee->save();
        
        return redirect()->route('employees.index', $company_id);
    }

    public function editdoc(Int $company_id, Int $employee_id, Request $request) { // Tipos de Documentos
        $editor = DB::table('users')
            ->where('users.id', Auth::user()->id)
            ->leftjoin('user_relations', 'users.id', '=', 'user_relations.id_user')
            ->leftjoin('companies', 'companies.id', '=', 'user_relations.id_company')
            ->select('users.*', 'companies.name AS company', 'companies.tipo AS tipo', 'user_relations.is_manager AS is_manager', 'companies.id as id_company')
            ->first();
        if(!($editor->type == 'Administrador' || $editor->type == 'Cliente' || $editor->type == 'Prestador')) abort(404, 'Access denied');
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

    public function updatedoc(Int $company_id, Int $employee_id, Request $request) {
        // dd($request);
        $editor = DB::table('users')
            ->where('users.id', Auth::user()->id)
            ->leftjoin('user_relations', 'users.id', '=', 'user_relations.id_user')
            ->leftjoin('companies', 'companies.id', '=', 'user_relations.id_company')
            ->select('users.*', 'companies.name AS company', 'companies.tipo AS tipo', 'user_relations.is_manager AS is_manager', 'companies.id as id_company')
            ->first();
        if(!($editor->type == 'Administrador' || $editor->type == 'Cliente' || $editor->type == 'Prestador')) abort(404, 'Access denied');
        $employee = Employee::findOrFail($employee_id);
        // if($editor->id_company != $employee->id_company) abort(403, 'Access denied');
        if($employee->documents){
            foreach (json_decode($employee->documents) as $document) {
                $old_document = DB::table('document_paths')
                    ->where('id_employee', $employee->id)
                    ->where('type', $document)
                    ->where('actual', 1)
                    ->first();
                if($request->{$document}) {
                    if(!(isset($request->new_due_date))) throw ValidationException::withMessages(['document_uploader' => 'Você deve enviar uma data de vencimento válida.', 'document_uploader_type'  => $document]);

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

                    $path = 'documents/'.$company->name.'/'.$employee->name;
                    $path = preg_replace('/[ -]+/' , '_' , strtolower( preg_replace("[^a-zA-Z0-9-]", "-", strtr(utf8_decode(trim($path)), utf8_decode("áàãâéêíóôõúüñçÁÀÃÂÉÊÍÓÔÕÚÜÑÇ"), "aaaaeeiooouuncAAAAEEIOOOUUNC-")) ));

                    $document_name = $document . '_' . $employee->id. "_" . $employee->name . ".{$extension}";
                    $document_name = preg_replace('/[ -]+/' , '_' , strtolower( preg_replace("[^a-zA-Z0-9-]", "-", strtr(utf8_decode(trim($document_name)), utf8_decode("áàãâéêíóôõúüñçÁÀÃÂÉÊÍÓÔÕÚÜÑÇ"), "aaaaeeiooouuncAAAAEEIOOOUUNC-")) ));

                    if((String) count($files = Storage::files($path)) == 0){
                        $document_name = '1_'.$document_name;
                    } else {
                        $document_name = (String) (count(Storage::files($path)) + 1).'_'.$document_name;
                    }

                    if($old_document){
                        if (Storage::exists($old_document->path . "/" . $old_document->name)) {
                            // $path_to_old = $old_document->path . "/old/";
                            // $name_to_old = (String) (count($files = Storage::files($old_document->path . "/old")) + 1). "_" . $old_document->name;
                            // dump($old_document->path . "/" . $old_document->name);
                            // dump($path_to_old . $name_to_old);
                            // dd($this);
                            // Storage::move($old_document->path . "/" . $old_document->name, $path_to_old . $name_to_old);
                            // dd($this);
                            $old_document = Document_path::findOrFail($old_document->id);
                            // $old_document->path = $path_to_old;
                            // $old_document->name = $name_to_old;
                            $old_document->status = 'Substituído';
                            $old_document->actual = 0;
                            $old_document->update();
                        }
                    }
                    $resultStore = $request->{$document}->storeAs($path, $document_name);
                    $document_path_model = array(
                        'path' => $path,
                        'due_date' => $request->new_due_date,
                        'name' => $document_name,
                        'type' => $document,
                        'status' => 'Pendente',
                        'sended_by' => $editor->name,
                        'actual' => 1,
                        'id_employee' => $employee->id,
                    );
                    $document_path = Document_path::create($document_path_model);
                    break;
                } else {
                    if($request->{'approve'.$document}) {
                        if($request->{'approve'.$document} == 'yes') {
                            $old_document = Document_path::findOrFail($old_document->id);
                            $old_document->status = 'Aprovado';
                            $old_document->aproved_by = $editor->name;
                            $old_document->update();
                            break;
                        } else {
                            if($request->{'approve'.$document} == 'no') {
                                $old_document = Document_path::findOrFail($old_document->id);
                                $old_document->status = 'Reprovado';
                                $old_document->aproved_by = $editor->name;
                                $old_document->desc = $request->disapproveDescription;
                                $old_document->update();
                                break;
                            }
                        }
                    } else {
                        if($request->{'remove'.$document}) {
                            if($request->{'remove'.$document} == 'yes') {
                                if (Storage::exists($old_document->path . "/" . $old_document->name)) {
                                    $old_document = Document_path::findOrFail($old_document->id);
                                    $old_document->status = 'Excluído';
                                    $old_document->actual = 0;
                                    $old_document->update();
                                    break;
                                }
                            }
                        } else {
                            if($old_document) {
                                if($old_document->type == $request->modal_type) {
                                    if($request->old_due_date) {
                                        if($request->old_due_date != $old_document->due_date) {
                                            $old_document = Document_path::findOrFail($old_document->id);
                                            $old_document->due_date = $request->old_due_date;
                                            $old_document->update();
                                            break;
                                        }
                                    }
                                }
                            } else {
                                throw ValidationException::withMessages(['document_uploader' => 'Você deve enviar um arquivo!', 'document_uploader_type'  => $request->modal_type]);
                            }
                        }
                    }
                }
            }
        }
        return redirect()->route('employees.edit', [$company_id, $employee_id]);
    }
}
