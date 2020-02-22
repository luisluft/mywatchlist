<?php

namespace App\Http\Controllers;

use App\Profile;
use App\User;
use Illuminate\Http\Request;

class ProfileController extends Controller
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

    public function index($profile_id)
    {
        $movies = Profile::find($profile_id)->movies;

        return view('pages.profile')->with('movies', $movies);
    }

    public function store($request)
    {
        var_dump($request);
    }
}
