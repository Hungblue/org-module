<?php

namespace KeyHoang\OrgModule\Console\Commands;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use KeyHoang\OrgModule\Events\SyncUserEvent;

class SyncUserCommand extends CommandBase
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'user:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync user.';

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
        try {
            $token = $this->getToken();
            if (!$token) {
                return;
            }

            $url  = config('organization.organization_url') . "/public/api/public/user/list";
            $page = 1;
            do {
                $lists  = [];
                $params = [
                    "page"  => $page,
                    "limit" => config('organization.organization_sync_limit')
                ];

                $response = Http::withToken($token)->get($url, $params);
                if ($response->status() == 200) {
                    $users = json_decode($response->body());
                    $lists = $users->data ?? [];
                }

                if (count($lists)) {
                    SyncUserEvent::dispatch($lists);
                }

                $page++;
            } while (count($lists) > 1);

            Log::info("Sync user success");
        } catch (Exception $exception) {
            Log::error("Sync user error. Exception :" . $exception->getMessage());
        }
    }
}
