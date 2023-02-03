<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TestController extends Controller
{
    // 追加
    public function index()
    {
        return view('tests.test');
    }
}
