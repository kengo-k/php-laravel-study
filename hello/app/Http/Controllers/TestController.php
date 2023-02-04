<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use  App\Models\Test;

class TestController extends Controller
{
    // 追加
    public function index()
    {
        $values = Test::All();
        // 取得したデータをdd関数に渡す
        dd($values);
        return view('tests.test', compact('values'));
    }
}
