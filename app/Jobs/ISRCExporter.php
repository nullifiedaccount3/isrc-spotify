<?php

namespace App\Jobs;

use App\Exports;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use SpotifyWebAPI\Session;
use SpotifyWebAPI\SpotifyWebAPI;

class ISRCExporter implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $inputs;

    protected $api;

    protected $user;

    protected $session;

    protected $data;

    protected $tsv;

    protected $index;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($inputs)
    {
        $this->inputs = $inputs;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->api = new SpotifyWebAPI();
        $this->user = User::find($this->inputs['user_id']);

        //Export ISRC data to file
        $this->api->setAccessToken($this->user->spotify_token);
        $limit = 50;
        $offset = 0;
        $this->index = 1;

        $tsv_header = implode("\t", [
            'Index',
            'Track Album Art',
            'Artist name',
            'Album Name',
            'Track Name',
            'ISRC',
            'Track Duration'
        ]);

        Storage::disk('local')->put($this->inputs['query'] . '.tsv', $tsv_header);

        $this->data = $this->api->search($this->inputs['query'], 'track', [
            'limit' => $limit,
            'offset' => $offset
        ]);
        $this->fetch_isrc();

        while (!empty($this->data->tracks->items)) {
            $offset = $offset + $limit;
            $this->refresh_token();
            $this->data = $this->api->search($this->inputs['query'], 'track', [
                'limit' => $limit,
                'offset' => $offset
            ]);
            $this->fetch_isrc();
        }

        $export = Exports::where('search_query', $this->inputs['query'])->first();

        if ($export->count() != 0) {
            $export->job_complete = 1;
            $export->save();
        }
    }

    private function fetch_isrc()
    {
        $tsv = null;
        foreach ($this->data->tracks->items as $track) {
            $artists = collect($track->album->artists)->map(function ($artist) {
                return isset($artist->name) ? $artist->name : '';
            })->toArray();
            $artists = implode(", ", $artists);
            try {
                $tsv = implode("\t", [
                    $this->index,
                    isset($track->album->images[0]->url) ? $track->album->images[0]->url : '',
                    $artists,
                    isset($track->album->name) ? $track->album->name : '',
                    isset($track->name) ? $track->name : '',
                    isset($track->external_ids->isrc) ? $track->external_ids->isrc : '',
                    isset($track->duration_ms) ? round($track->duration_ms / 60000, 2) : ''
                ]);
                $this->index = $this->index + 1;
                Storage::disk('local')->append($this->inputs['query'] . '.tsv', $tsv);
            } catch (\Exception $exception) {
                Log::error(json_encode($track));
            }
        }
    }

    private function refresh_token()
    {
        //Refresh token
        $this->api->setAccessToken($this->user->spotify_token);
        $this->session = new Session(env('SPOTIFY_KEY'), env('SPOTIFY_SECRET'), env('SPOTIFY_REDIRECT_URI'));

        try {
            json_encode($this->api->me());
        } catch (\Exception $exception) {
            if ($this->session->refreshAccessToken($this->user->spotify_refresh_token)) {
                $this->api->setAccessToken($this->session->getAccessToken());
                $this->user->spotify_token = $this->session->getAccessToken();
                $this->user->save();
            }
        }
    }
}
