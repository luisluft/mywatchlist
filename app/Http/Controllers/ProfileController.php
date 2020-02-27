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
        $profile = Profile::find($profile_id);
        $movies  = $profile->movies;
        $user_id = auth()->user()->id;
        $user    = User::find($user_id);

        return view('pages.profile')->with('movies', $movies)->with('user', $user)->with('profile', $profile);
    }

    public function store(Request $request)
    {
        $user_id          = auth()->user()->id;
        $profile_title    = $request->profile_title;
        $profile          = new Profile();
        $profile->user_id = $user_id;
        $profile->list_id = $request->list_id;
        $profile->title   = $profile_title;
        $profile->save();

        return "saved profile with list_id = $request->list_id into the user with id = $user_id";
    }

    /**
     * Inserts the user created session_id in the table for later usage in watchlaters
     */
    public function update(Request $request)
    {
        $profile_id          = $request->input('profile_id');
        $profile             = Profile::find($profile_id);
        $profile->session_id = $request->session_id;
        $profile->save();

        return "saved session: $profile->session_id into profile with id: $profile_id";
    }

    public function delete(Request $request)
    {
        $profile_id = $request->profile_id;
        Profile::where('list_id', $profile_id)->delete();
        return response("successfully deleted list with id = $profile_id", 200);
    }
}
