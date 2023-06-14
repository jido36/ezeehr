<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Admin\AdmiUserController;
use App\Http\Controllers\testController;
use App\Http\Controllers\Admin\AuthenticationController;
use App\Http\Controllers\Admin\JobsController;
use App\Http\Controllers\Admin\CompanyController;
use App\Http\Controllers\Admin\RolesController;
use App\Http\Controllers\Admin\PagesController;
use App\Http\Controllers\EducationController;
use App\Http\Controllers\CertificationController;
use App\Http\Controllers\LinkController;
use App\Http\Controllers\WorkExperienceController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\CandidatesBioController;
use App\Http\Controllers\Admin\ApplicationsController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware(['auth:api', 'verified'])->group(function () {
    // return $request->user();
    Route::post('/education', [EducationController::class, 'store']);
    Route::post('/link', [LinkController::class, 'store']);
    Route::post('/workexperience', [WorkExperienceController::class, 'store']);
    Route::post('/certification', [CertificationController::class, 'store']);
    Route::post('/apply', [ApplicationController::class, 'apply']);
    Route::post('/upload-cv', [ApplicationController::class, 'uploadDocument']);
    Route::post('/candidatebio', [CandidatesBioController::class, 'store']);
    Route::get('/applications', [ApplicationController::class, 'index']);
});

Route::get('job/{id}', [ApplicationController::class, 'viewJob']);

Route::get('test', [testController::class, 'testView']);




// tokenexpired
Route::get('/tokenexpired', function () {
    return response()->json(['message' => "Token is expired"], 401);
})->name('tokenexpired');

Route::post('/register', [UserController::class, 'create']);
Route::post('/login', [UserController::class, 'login']);
Route::post('/auth', [UserController::class, 'authenticate']);
Route::post('/logout', [UserController::class, 'logout'])->middleware('auth:sanctum');
// Route::post('/gettoken', [UserController::class, 'getToken'])->middleware('auth:api');
Route::post('/gettoken', [UserController::class, 'getToken']);
Route::post('/getclients', [UserController::class, 'getClients']);

Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth:api')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    // return redirect('/home');
    echo "verified";
})->middleware(['auth:api', 'signed'])->name('verification.verify');


Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();

    return back()->with('message', 'Verification link sent!');
})->middleware(['auth:api', 'throttle:6,1'])->name('verification.send');

// Route::prefix('admin')->middleware(['auth:api'])->group(function () {

// }

Route::prefix('admin')->group(function () {
    Route::post('/createuser', [AdmiUserController::class, 'createUser']);
    Route::post('/login', [AuthenticationController::class, 'login']);
    Route::post('/auth', [AuthenticationController::class, 'authenticate']);
    Route::get('/test', [testController::class, 'testValidation']);
    // Route::post('/logout', [UserController::class, 'logout']);
    Route::middleware(['auth:api-admin'])->group(function () {
        Route::post('/create-company', [CompanyController::class, 'createCompany']);
        Route::any('/assign-company', [CompanyController::class, 'assignCompany']);
        Route::get('/dashboard', [PagesController::class, 'dashboard']);
        Route::post('/create-job', [JobsController::class, 'createJob']);
        Route::post('/update-job', [JobsController::class, 'updateJob']);
        Route::post('/get-job', [JobsController::class, 'getJob']);
        Route::get('/view-jobs', [JobsController::class, 'index']);
        Route::post('/add-user', [AdmiUserController::class, 'addUser']);
        Route::post('/add-role', [RolesController::class, 'createRole']);
        Route::post('/assign-role', [RolesController::class, 'assignRole']);

        // list applications
        Route::get('/applications', [ApplicationsController::class, 'listAllApplications']);
        Route::post('/getapplication', [ApplicationsController::class, 'getCandidateApplication']);
        Route::post('/comment', [ApplicationsController::class, 'addComment']);
        Route::post('/update-application', [ApplicationsController::class, 'updateApplication']);
        // test dashboard without authentication.
    });
});
