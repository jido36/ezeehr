<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\CreateCompanyRequest;
use App\Http\Services\Admin\CompanyService;
use App\Http\Services\Admin\AuthorisationService;

class CompanyController extends Controller
{
    public function __construct(AuthorisationService $authorisationservice)
    {
        try {
            $authorisationservice->accessCompany();
        } catch (\Exception $e) {
            abort(400, "you do not have the rigth to create or modify the comany records.");
        }
    }
    public function createCompany(CreateCompanyRequest $request, CompanyService $companyservice)
    {
        $validated = $request->validate();

        $company = $companyservice->createCompany($validated);

        return $company;
    }

    public function assignCompany(Request $request, CompanyService $companyservice)
    {
        return $companyservice->assignCompany($request);
    }
}
