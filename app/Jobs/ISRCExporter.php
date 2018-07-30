<?php

namespace App\Jobs;

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

        $this->refresh_token();

        //Export ISRC data to file
        $this->api->setAccessToken($this->user->spotify_token);
        $limit = 50;
        $offset = 0;
        $this->index = 1;

        Storage::disk('local')->put($this->inputs['query'] . '.tsv', null);
        $this->tsv = implode("\t", ['Index',
            'Track Album Art',
            'Artist name',
            'Album Name',
            'Track Name',
            'ISRC',
            'Track Duration']);

        $this->data = $this->api->search($this->inputs['query'], 'track', [
            'limit' => $limit,
            'offset' => $offset
        ]);

        while (!empty($this->data->tracks->items)) {
            $this->fetch_isrc();
            $offset = $offset + $limit + 1;
            $this->data = $this->api->search($this->inputs['query'], 'track', [
                'limit' => $limit,
                'offset' => $offset
            ]);
        }
    }

    private function fetch_isrc()
    {
        $this->refresh_token();
        foreach ($this->data->tracks->items as $track) {
            $this->tsv = $this->tsv . PHP_EOL;
            $artists = collect($track->album->artists)->map(function ($artist) {
                return $artist->name;
            })->toArray();
            $artists = implode(", ", $artists);
            try {
                $this->tsv = $this->tsv . implode("\t", [$this->index,
                        $track->album->images[0]->url,
                        $artists,
                        $track->album->name,
                        $track->name,
                        $track->external_ids->isrc,
                        round($track->duration_ms / 60000, 2)]);
                $this->index = $this->index + 1;
            } catch (\Exception $exception) {
                Log::error(json_encode($track));
            }
        }
        Storage::disk('local')->append($this->inputs['query'] . '.tsv', $this->tsv);
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
