<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\Company;
use Illuminate\Support\Facades\Auth;
use App\Models\Admin\AdminUserCompany;
use App\Models\Admin\AdminUser;
use Exception;
use Illuminate\Support\Facades\Log;


class CompanyController extends Controller
{
    public function createCompany(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|unique:companies,name',
            'size' => 'integer',
            'email' => 'unique:companies,email',
        ]);

        $user = AdminUser::find(Auth::id())->first();

        $data = [
            'name' => $validated['name'],
            'size' => $validated['size'],
            'email' => $validated['email'],
            'created_by' => Auth::id(),
            'entity_id' => $user->entity_id,
        ];

        try {
            $company = Company::create($data);
            AdminUserCompany::create([
                'admin_user_id' => Auth::id(),
                'company_id' => $company->id,
            ]);
            return response(['status' => true, 'message' => 'Record created'], 200);
        } catch (Exception $e) {
            Log::error($e);
            return response(['status' => false, 'message' => 'Error creating record'], 422);
        }
    }
}
