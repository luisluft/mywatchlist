<?php

namespace App\Http\Controllers;

use App\Profile;
use App\User;
use Illuminate\Http\Request;

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
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $user_id  = auth()->user()->id;
        $profiles = User::find($user_id)->profiles;
        $user = User::find($user_id);

        return view('pages.home')->with('profiles', $profiles)->with('user', $user);
    }

    public function saveAccessToken(Request $request)
    {
        $user_id       = auth()->user()->id;
        $user = User::find($user_id);
        $user->access_token = $request->access_token;
        $user->save();

        return "saved $request->access_token into user with id = $user_id";
    }
}
