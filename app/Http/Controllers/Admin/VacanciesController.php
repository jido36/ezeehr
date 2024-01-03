<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;
use App\http\Services\Admin\AuthorisationService;
use App\Http\Services\Admin\VacanciesService;
use App\Http\Requests\Admin\CreateJobRequest;

class VacanciesController extends Controller
{

    public function index(AuthorisationService $authorisationservice, VacanciesService $vacanciesservice)
    {

        try {

            $authorisationservice->viewJobs();
        } catch (Exception $e) {
            abort(422, "You do not have the rights to view this page");
        }

        $listJobs = $vacanciesservice->listAllVacancies();
        return response()->json(['status' => true, 'data' => $listJobs], 200);
    }

    public function createJob(CreateJobRequest $request, AuthorisationService $authorisationservice, VacanciesService $vacanciesservice)
    {
        try {
            $authorisationservice->updateJobs();
        } catch (Exception $e) {
            abort(422, "You do not have the access right to view this module");
        }

        // $validated = $request->validate();

        return $vacanciesservice->createJobs($request);
    }

    public function updateJob(CreateJobRequest $request, AuthorisationService $authorisationservice, VacanciesService $vacanciesservice)
    {
        try {
            $authorisationservice->updateJobs();
        } catch (Exception $e) {
            abort(422, "You do not have the access right to view this module");
        }

        // $validated = $request->validate();

        return $vacanciesservice->updateJob($request);
    }

    public function getJob($job_id, AuthorisationService $authorisationservice, VacanciesService $vacanciesservice)
    {
        try {

            $authorisationservice->viewJobs();
        } catch (Exception $e) {
            abort(422, "You do not have the rights to view this page");
        }

        return $vacanciesservice->getJob($job_id);
    }
}
