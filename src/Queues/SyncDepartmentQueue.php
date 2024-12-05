<?php

namespace KeyHoang\OrgModule\Queues;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\Jobs\Job;
use Illuminate\Support\Facades\Log;
use KeyHoang\OrgModule\Services\DepartmentService;
use KeyHoang\OrgModule\Services\UserService;

class SyncDepartmentQueue implements ShouldQueue
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
        $department = $data["department"] ?? [];
        Log::info('Sync department:' . json_encode($data));
        (new DepartmentService())->sync((object)$department);
    }
}
