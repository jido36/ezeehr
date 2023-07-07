<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\LoginRequest;
use App\Http\Services\Admin\AuthenticationService;


class AuthenticationController extends Controller
{
    public function login(LoginRequest $request, AuthenticationService $authenticationservice)
    {
        $validated = $request->validated();
        $authenticate = $authenticationservice->Login($validated);
        return $authenticate;
    }
}
