<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CreateUserRequest;
use App\Http\Services\Admin\AdminUserService;
use App\Http\Requests\Admin\AddUserRequest;


class AdmiUserController extends Controller
{
    //Create administrator


    public function createUser(CreateUserRequest $request, AdminUserService $adminUserService)
    {
        $validated = $request->validated();

        $user = $adminUserService->createUser($validated);

        return $user;
    }

    // adds a new user to admins company
    public function addUser(AddUserRequest $request, AdminUserService $adminUserService)
    {
        $validated = $request->validated();

        $user = $adminUserService->addUser($validated);

        return $user;
    }
}
