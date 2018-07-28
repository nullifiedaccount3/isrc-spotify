<?php

namespace App\Jobs;

use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use SpotifyWebAPI\SpotifyWebAPI;

class ISRCExporter implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $inputs;

    protected $api;

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
        $this->api->setAccessToken(User::find($this->inputs['user_id'])->spotify_token);
        file_put_contents(base_path('tmp/' . $this->inputs['query'] . '.json'),
            json_encode($this->api->search($this->inputs['query'], 'track', [
                'limit' => 39
            ])));
    }
}
