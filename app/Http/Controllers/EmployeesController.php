<?php

namespace App\Http\Controllers;

use Illuminate\Validation\ValidationException;
use App\Http\Requests\UpdateEmployeeRequest;
use App\Http\Requests\StoreEmployeeRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Company_relation;
use App\Models\Responsibility;
use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Company;
use App\Models\Sector;


class EmployeesController extends Controller {
    public function index() {
        $editor = DB::table('users')
            ->where('users.id', Auth::user()->id)
            ->join('user_relations', 'users.id', '=', 'user_relations.id_user')
            ->join('companies', 'companies.id', '=', 'user_relations.id_company')
            ->select('users.*', 'companies.nome_fantasia AS company', 'companies.tipo AS tipo', 'user_relations.is_manager AS is_manager', 'companies.id as id_company')
            ->first();

            
        $employees = DB::table('employees')
            ->where('responsibilities.id_company', $editor->id_company)
            ->join('companies', 'companies.id', '=', 'employees.id_company')
            ->join('responsibilities', 'responsibilities.id', '=', 'employees.id_responsibility')
            ->join('sectors', 'sectors.id', '=', 'employees.id_sector')
            ->select('employees.*', 'companies.nome_fantasia AS company', 'responsibilities.name AS responsibility', 'sectors.name AS sector')
            ->paginate(9);
        $sectors = DB::table('employees')
            ->get();
        
        $responsibilities = DB::table('employees')
            ->get();

        if (Auth::user()->type == 'Administrador') {
            $employees = DB::table('employees')
                ->join('companies', 'companies.id', '=', 'employees.id_company')
                ->join('responsibilities', 'responsibilities.id', '=', 'employees.id_responsibility')
                ->join('sectors', 'sectors.id', '=', 'employees.id_sector')
                ->select('employees.*', 'companies.nome_fantasia AS company', 'responsibilities.name AS responsibility', 'sectors.name AS sector')
                ->paginate(9);
        }
        return view('employees.index', compact('employees', 'sectors', 'responsibilities'));
    }
    
    public function create() {
        $editor = DB::table('users')
            ->where('users.id', Auth::user()->id)
            ->join('user_relations', 'users.id', '=', 'user_relations.id_user')
            ->join('companies', 'companies.id', '=', 'user_relations.id_company')
            ->select('users.*', 'companies.nome_fantasia AS company', 'companies.tipo AS tipo', 'user_relations.is_manager AS is_manager', 'companies.id as id_company')
            ->first();
        
        if(Auth::user()->type == 'Administrador') {
            $companies = Company::all();
            $sectors = Sector::all();
            $responsibilities = Responsibility::all();
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

                $sectors = DB::table('sectors')
                    ->join('companies', 'companies.id', '=', 'sectors.id_company')
                    ->join('company_relations', function($join) {
                        $join
                            ->on('companies.id', '=', 'company_relations.id_contratada')
                            ->orOn('companies.id', '=', 'company_relations.id_contratante');
                    })
                    ->select('sectors.*', 'companies.nome_fantasia AS company', 'companies.tipo AS tipo')
                    ->paginate(9);
                $responsibilities = DB::table('responsibilities')
                    ->join('companies', 'companies.id', '=', 'responsibilities.id_company')
                    ->join('company_relations', function($join) {
                        $join
                            ->on('companies.id', '=', 'company_relations.id_contratada')
                            ->orOn('companies.id', '=', 'company_relations.id_contratante');
                    })
                    ->select('responsibilities.*', 'companies.nome_fantasia AS company', 'companies.tipo AS tipo')
                    ->paginate(9);
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
                    
                $responsibilities = DB::table('responsibilities')
                    ->join('companies', 'companies.id', '=', 'responsibilities.id_company')
                    ->join('company_relations', function($join) {
                        $join
                            ->on('companies.id', '=', 'company_relations.id_contratada')
                            ->orOn('companies.id', '=', 'company_relations.id_contratante');
                    })
                    ->select('responsibilities.*', 'companies.nome_fantasia AS company', 'companies.tipo AS tipo')
                    ->paginate(9);
                $sectors = DB::table('sectors')
                    ->join('companies', 'companies.id', '=', 'sectors.id_company')
                    ->join('company_relations', function($join) {
                        $join
                            ->on('companies.id', '=', 'company_relations.id_contratada')
                            ->orOn('companies.id', '=', 'company_relations.id_contratante');
                    })
                    ->select('sectors.*', 'companies.nome_fantasia AS company', 'companies.tipo AS tipo')
                    ->paginate(9);
            }
        }
        return view('employees.create', compact('companies', 'sectors', 'responsibilities'));
    }
    
    public function store(StoreEmployeeRequest $request) {
        $req = $request->validated();
        $new_employee = Employee::create($req);

        return redirect()->route('employees.index');
    }
    
    public function show(Employee $employee) {
        $employee = DB::table('employees')
            ->where('employees.id', $employee->id)
            ->join('companies', 'companies.id', '=', 'employees.id_company')
            ->join('responsibilities', 'responsibilities.id', '=', 'employees.id_responsibility')
            ->join('sectors', 'sectors.id', '=', 'employees.id_sector')
            ->select('employees.*', 'companies.nome_fantasia AS company', 'responsibilities.name AS responsibility', 'sectors.name AS sector')
            ->first();
        
        return view('employees.show', compact('employee'));
    }

    public function edit(Employee $employee) {
        $employee = DB::table('employees')
            ->where('employees.id', $employee->id)
            ->join('companies', 'companies.id', '=', 'employees.id_company')
            ->join('responsibilities', 'responsibilities.id', '=', 'employees.id_responsibility')
            ->join('sectors', 'sectors.id', '=', 'employees.id_sector')
            ->select('employees.*', 'companies.nome_fantasia AS company', 'responsibilities.name AS responsibility', 'sectors.name AS sector', 'responsibilities.documents AS documents')
            ->first();
        $documents = DB::table('documents')
            ->join('companies', 'companies.id', '=', 'documents.id_company')
            ->join('company_relations', function($join) {
                $join
                    ->on('companies.id', '=', 'company_relations.id_contratada')
                    ->orOn('companies.id', '=', 'company_relations.id_contratante');
            })
            ->select('documents.*', 'companies.nome_fantasia AS company', 'companies.tipo AS tipo')
            ->get();
        $companies = Company::all();

        $sectors = Sector::all();
        
        $responsibilities = Responsibility::all();

        return view('employees.edit', compact('employee', 'documents', 'companies', 'sectors', 'responsibilities'));
    }
    
    public function update(UpdateEmployeeRequest $request, Employee $employee) {
        $req = $request->validated();
        $employee->update($req);
        return redirect()->route('employees.index');
    }
    
    public function destroy(Employee $employee) {
        $employee->active = 0;
        
        $employee->save();
        
        return redirect()->route('employees.index');
    }
}