<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     *
     *
     */

    //  'id',
    //  'job_id',
    //  'application_id',
    //  'cv_id',
    //  'cover_letter_id',
    //  'applicant_id'
    public function up()
    {
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->integer('job_id')->references('id')->on('vacancies');
            // $table->uuid('cv_id');
            // $table->uuid('cover_letter');
            $table->integer('cv_id')->references('id')->on('documents');
            $table->integer('coverletter_id')->references('id')->on('documents');
            $table->integer('applicant_id')->references('id')->on('users');
            $table->timestamps();
            $table->string('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('applications');
    }
};
