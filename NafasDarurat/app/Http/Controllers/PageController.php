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
        return view('admin.layouts.cek');
    }

    public function laporGejala()
    {
        return view('admin.layouts.lapor-gejala');
    }

    public function laporApi()
    {
        return view('admin.layouts.lapor-api');
    }
}
