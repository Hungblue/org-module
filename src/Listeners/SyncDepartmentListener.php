<?php

namespace KeyHoang\OrgModule\Listeners;

use Illuminate\Support\Facades\Log;
use KeyHoang\OrgModule\Events\SyncDepartmentEvent;
use KeyHoang\OrgModule\Services\DepartmentService;
use Throwable;

class SyncDepartmentListener
{

    public ?string $queue = 'sync-department-listener';

    /**
     * Handle the event.
     *
     * @param SyncDepartmentEvent $event
     *
     * @return void
     */
    public function handle(SyncDepartmentEvent $event): void
    {
        $departments       = $event->data;
        $departmentService = new DepartmentService();
        foreach ($departments as $department) {
            $departmentService->sync($department);
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(SyncDepartmentEvent $event, Throwable $exception): void
    {
        Log::error($exception->getMessage(), $event->data->toArray());
    }

}
