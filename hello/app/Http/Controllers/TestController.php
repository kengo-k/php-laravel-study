<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use  App\Models\Test;

class TestController extends Controller
{
    // 追加
    public function index()
    {
        $values = Test::all();
        $first = Test::findOrFail(1);
        dd($first);
        return view('tests.test', compact('values'));
    }
}
