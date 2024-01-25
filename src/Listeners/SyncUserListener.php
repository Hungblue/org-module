<?php

namespace src\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use src\Events\SyncUserEvent;
use src\Services\UserService;
use Throwable;

class SyncUserListener implements ShouldQueue
{

    public ?string $queue = 'sync-user-listener';

    /**
     * Handle the event.
     *
     * @param SyncUserEvent $event
     *
     * @return void
     */
    public function handle(SyncUserEvent $event): void
    {
        $users       = $event->data;
        $userService = new UserService();
        foreach ($users as $user) {
            $userService->sync($user);
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(SyncUserEvent $event, Throwable $exception): void
    {
        Log::error($exception->getMessage(), $event->data->toArray());
    }
}
