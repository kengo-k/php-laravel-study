<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use  App\Models\Test;

use Illuminate\Support\Facades\DB;

class TestController extends Controller
{
    // 追加
    public function index()
    {
        $values = DB::table('tests')
        ->where('text', '=', 'Hello!')
        ->select('id', 'text')
        ->get();
        dd($values);
        return view('tests.test', compact('values'));
    }
}
