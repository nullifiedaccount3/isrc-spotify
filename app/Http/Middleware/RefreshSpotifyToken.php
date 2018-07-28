<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\UnauthorizedException;
use SpotifyWebAPI\Session;
use SpotifyWebAPI\SpotifyWebAPI;

class RefreshSpotifyToken
{
    protected $session;

    protected $api;

    protected $user;

    public function __construct(Guard $guard)
    {
        $this->user = Auth::loginUsingId(1);
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $this->api = new SpotifyWebAPI();
        $this->api->setAccessToken($this->user->spotify_token);
        $this->session = new Session(env('SPOTIFY_KEY'), env('SPOTIFY_SECRET'), env('SPOTIFY_REDIRECT_URI'));

        try {
            json_encode($this->api->me());
        } catch (\Exception $exception) {
            if ($this->session->refreshAccessToken($this->user->spotify_refresh_token)) {
                $this->api->setAccessToken($this->session->getAccessToken());
                $this->user->spotify_token = $this->session->getAccessToken();
                $this->user->save();
            } else {
                Auth::logout();
                Redirect::home();
            }
        }

        return $next($request);
    }
}
