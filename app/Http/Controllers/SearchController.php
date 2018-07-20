<?php

namespace App\Http\Controllers;

use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use SpotifyWebAPI\Session;
use SpotifyWebAPI\SpotifyWebAPI;

class SearchController extends Controller
{
    protected $session;

    protected $api;

    protected $accessToken;

    public function __construct()
    {
        //
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->session = new Session(env('SPOTIFY_KEY'), env('SPOTIFY_SECRET'), env('SPOTIFY_REDIRECT_URI'));
        $this->api = new SpotifyWebAPI();
        $this->session->refreshAccessToken(Auth::user()->spotify_refresh_token);
        $this->accessToken = $this->session->getAccessToken();
        $user = User::find(Auth::user()->id);
        $user->spotify_token = $this->accessToken;
        $user->spotify_token_expiry = Carbon::now(config('app.timezone'))->addSeconds($this->session->getTokenExpiration())->toDateTimeString();
        $user->save();
        $this->api->setAccessToken($this->accessToken);

        $query = Input::get('q');
        return json_encode($this->api->search($query, 'track'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
