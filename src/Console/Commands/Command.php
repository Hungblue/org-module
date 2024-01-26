<?php

namespace KeyHoang\OrgModule\Console\Commands;

use Illuminate\Bus\Queueable;
use Illuminate\Console\Command;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Command extends Command
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
    }

    public function getToken()
    {
        $url    = config('organization.organization_url') . '/public/oauth/token';
        $params = [
            'grant_type'    => config('organization.organization_grant_type'),
            'client_id'     => config('organization.organization_client_id'),
            'client_secret' => config('organization.organization_client_secret')
        ];

        Log::info("Start Login Organization - url: $url", $params);
        $response = Http::asForm()->post($url, $params);
        if ($response->status() == 200) {
            $client = json_decode($response->body());
            Log::info("Login success: " . $response->body());

            return $client->access_token;
        }
        else {
            Log::info("Login fail");

            return null;
        }
    }
}
