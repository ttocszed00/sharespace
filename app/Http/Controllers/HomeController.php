<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Files;


class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $Files = Files::all()->where('id', '=', Auth::user()->id);
        $Public_Files = Files::all()->where('id', '=', null);
         return view('home')->with([
            "Files" => $Files,
             "Public_Files" => $Public_Files,
        ]);
    }
}




