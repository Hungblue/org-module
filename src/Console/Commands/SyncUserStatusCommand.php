<?php

namespace KeyHoang\OrgModule\Console\Commands;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use KeyHoang\OrgModule\Services\UserStatusService;

class SyncUserStatusCommand extends CommandBase
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'user-status:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync user status.';

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
        $url = config('organization.organization_url') . "/public/api/public/user-status";
        $this->syncUserStatus($url, 'user status');
    }

    public function syncUserStatus($url, $name): void
    {
        try {
            $token = $this->getToken();
            if (!$token) {
                return;
            }

            $response = Http::withToken($token)->get($url);
            if ($response->status() == 200) {
                $results = json_decode($response->body());
                (new UserStatusService())->sync($results);
            }

            Log::info("Sync $name success");
        } catch (Exception $exception) {
            Log::error("Sync $name error. Exception :" . $exception->getMessage());
        }
    }
}
