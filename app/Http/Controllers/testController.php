<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class testController extends Controller
{
    //

    public function testValidation(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'author_name' => 'required',
            'author_description' => 'required',
        ]);

        return $request->all();
    }
}