<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\User;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Laravel\Socialite\Facades\Socialite;
use SpotifyWebAPI\SpotifyWebAPI;

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
        $this->middleware('guest', ['except' => ['logout', 'getLogout']]);
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
        return Input::get();
//        $oauth_object = Socialite::driver('spotify');
//        $user = $oauth_object->user();
        $session = new \SpotifyWebAPI\Session(
            env('SPOTIFY_KEY'),
            env('SPOTIFY_SECRET'),
            env('SPOTIFY_REDIRECT_URI')
        );
        $session->requestAccessToken(Input::get('code'));
        $accessToken = $session->getAccessToken();
        $refreshToken = $session->getRefreshToken();
        $api = new SpotifyWebAPI();
        $api->setAccessToken($accessToken);

        $spot_user = $api->me();

//        return Response::json($user->images[0]->url);
        if (User::where('spotify_id', $spot_user->id)->count() == 0) {
            $user = new User();
            $user->spotify_id = $spot_user->id;
        } else {
            $user = User::where('spotify_id', $spot_user->id)->first();
        }
        $user->name = $spot_user->display_name;
        $user->nickname = $spot_user->display_name;
        $user->avatar = $spot_user->images[0]->url;
        $user->spotify_token = $accessToken;
        $user->spotify_refresh_token = $refreshToken;
        $user->spotify_profile_url = $spot_user->external_urls->spotify;
        $user->spotify_profile_api_url = $spot_user->href;
        $user->spotify_token_expiry = Carbon::createFromTimestamp($session->getTokenExpiration())->toDateTimeString();
        $user->save();
        Auth::login($user, true);
        return redirect()->to('/exporter');
    }

    public function logout()
    {
        Auth::logout();
        return Redirect::to(' / ')->send();
    }
}
