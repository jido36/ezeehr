<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Blade;

class testController extends Controller
{
    //

    public function testValidation(Request $request)
    {
        echo "hello world";
        die;
        $request->validate([
            'title' => 'required',
            'author_name' => 'required',
            'author_description' => 'required',
        ]);

        return $request->all();
    }
}
