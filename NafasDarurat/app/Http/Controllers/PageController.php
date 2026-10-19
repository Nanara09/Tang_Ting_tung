<?php

namespace App\Http\Controllers;

class PageController extends Controller
{
    public function home()
    {
        return view('admin.layouts.home');
    }

    public function cek()
    {
        return view('cek');
    }

    public function laporGejala()
    {
        return view('lapor-gejala');
    }

    public function laporApi()
    {
        return view('lapor-api');
    }
}
