<?php

namespace App\Http\Controllers;

use Illuminate\Validation\ValidationException;
use App\Http\Requests\UpdateEmployeeRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Company_relation;
use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Company;


class EmployeesController extends Controller {
    public function index() {
        $employees = DB::table('employees')
            ->join('companies', 'companies.id', '=', 'employees.id_company')
            ->select('employees.*', 'companies.nome_fantasia AS company')
            ->paginate(9);

        return view('employees.index', compact('employees'));
    }
    
    public function create() {
        $companies = Company::all();
        return view('employees.create', compact('companies'));
    }
    
    public function store(UpdateEmployeeRequest $request) {
        $req = $request->validated();
        $new_employee = Employee::create($req);
        return redirect()->route('employees.index');
    }
    
    public function show(Employee $employee) {
        $employee = DB::table('employees')
            ->where('employees.id', $employee->id)
            ->join('companies', 'companies.id', '=', 'employees.id_company')
            ->select('employees.*', 'companies.nome_fantasia AS company')
            ->first();
        
        return view('employees.show', compact('employee'));
    }

    public function edit(Employee $employee) {
        $employee = DB::table('employees')
            ->where('employees.id', $employee->id)
            ->join('companies', 'companies.id', '=', 'employees.id_company')
            ->select('employees.*', 'companies.nome_fantasia AS company')
            ->first();
            
        $companies = Company::all();

        return view('employees.edit', compact('employee', 'companies'));
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