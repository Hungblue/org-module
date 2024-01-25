<?php

namespace src\Queues;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\Jobs\Job;
use Illuminate\Support\Facades\Log;
use src\Services\UserService;

class SyncUserQueue implements ShouldQueue
{

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
    }

    /**
     * Execute the job.
     */
    public function handle(Job $job, array $data): void
    {
        $user = $data["user"] ?? [];
        Log::info('Sync user:' . json_encode($data));
        (new UserService())->sync((object)$user);
    }
}
