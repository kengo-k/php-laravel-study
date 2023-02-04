<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use  App\Models\Test;

class TestController extends Controller
{
    // 追加
    public function index()
    {
        $values = Test::where('text', '=', 'Hello!');
        dd($values->get());
        return view('tests.test', compact('values'));
    }
}
