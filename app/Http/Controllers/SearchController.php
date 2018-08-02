<?php

namespace App\Http\Controllers;

use App\Exports;
use App\Jobs\ISRCExporter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Response;
use SpotifyWebAPI\SpotifyWebAPI;

class SearchController extends Controller
{
    protected $session;

    protected $api;

    public function __construct()
    {
        //
    }

    /**
     * Display a listing of the resource.
     *
     */
    public function index()
    {
        $this->api = new SpotifyWebAPI();
        $this->api->setAccessToken(Auth::user()->spotify_token);
        $inputs = [
            'user_id' => Auth::user()->id,
            'query' => Input::get('q')
        ];

        $this->dispatch(new ISRCExporter($inputs));

        $export = Exports::where('search_query', $inputs['query'])->first();

        if (!is_null($export)) {
            $export->job_complete = 0;
            $export->save();
        } else {
            $export = new Exports();
            $export->search_query = $inputs['query'];
            $export->filename = $inputs['query'] . '.tsv';
            $export->file = $inputs['query'] . '.tsv';
            $export->job_complete = 0;
            $export->save();
        }

        return Response::json([
            'message' => 'ISRC query job for ' . $inputs['query'] . ' is being processed'
        ]);
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
