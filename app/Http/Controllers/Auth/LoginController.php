<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\User;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Laravel\Socialite\Facades\Socialite;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Redirect the user to the GitHub authentication page.
     *
     * @return \Illuminate\Http\Response
     */
    public function redirectToProvider()
    {
        return Socialite::driver('spotify')->redirect();
    }

    /**
     * Obtain the user information from Spotify
     *
     * @return \Illuminate\Http\Response
     */
    public function handleProviderCallback()
    {
        $oauth_object = Socialite::driver('spotify');
        $oauth_user_object = $oauth_object->user();
        if (User::where('spotify_id', $oauth_user_object->id)->count() == 0) {
            $user = new User();
            $user->spotify_id = $oauth_user_object->id;
        } else {
            $user = User::where('spotify_id', $oauth_user_object->id)->first();
        }
        $user->name = $oauth_user_object->name;
        $user->nickname = $oauth_user_object->nickname;
        $user->email = $oauth_user_object->email;
        $user->avatar = $oauth_user_object->avatar;
        $user->spotify_token = $oauth_user_object->token;
        $user->spotify_refresh_token = $oauth_user_object->refreshToken;
        $user->spotify_profile_url = $oauth_user_object->user['external_urls']['spotify'];
        $user->spotify_profile_api_url = $oauth_user_object->profileUrl;
        $user->spotify_token_expiry = Carbon::now(config('app.timezone'))->addSeconds($oauth_user_object->expiresIn);
        $user->save();
        Auth::login($user, true);
        return redirect()->to('/exporter');
    }

    public function logout()
    {
        Auth::logout();
        return Redirect::to('/')->send();
    }
}
