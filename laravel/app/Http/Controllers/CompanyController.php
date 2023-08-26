<?php

namespace App\Http\Controllers;

use http\Env\Response;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\View\View;
use mysql_xdevapi\Table;

class CompanyController extends Controller
{
    public function getEmployeesOfCompany(string $companyId): View{
        $company_name = "not found";
        $company = DB::table('companies')->where('id', $companyId)->first();
        if ($company){
            $company_name = $company->name;
        }
        $employees = DB::table('employees')->where('company_id', $companyId)->paginate(10);
        return view('company.employees',['company_name' => $company_name, 'employees' => $employees]);
    }
    public function addEmployee(Request $request) :RedirectResponse{
        $company_name = "not found";
        $company = DB::table('companies')->where('id', $request->get('company_id'))->first();
        if ($company){
            $company_name = $company->name;
        }

        DB::table('employees')->insert([
            'company_id' => $request->get('company_id'),
            'firstname' => $request->get('firstname'),
            'lastname' => $request->get('lastname')
        ]);
        return redirect('/companyemployees/'.$request->get('company_id'));
    }
    public function updateEmployee(Request $request, string $id) :RedirectResponse{
        echo "company_id:" . $request->get('company_id');
        DB::table('employees')->where('id',$id)
        ->update([
            'company_id' => $request->get('company_id'),
            'firstname' => $request->get('firstname'),
            'lastname' => $request->get('lastname')
        ]);
        return redirect('/companyemployees/'.$request->get('company_id'));
    }
    public function deleteEmployee(int $employee_id) :RedirectResponse{
        DB::table('employees')->delete($employee_id);
        return redirect('/');
    }
    //
}
