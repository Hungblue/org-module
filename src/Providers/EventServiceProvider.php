<?php

namespace KeyHoang\OrgModule\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use KeyHoang\OrgModule\Events\SyncDepartmentEvent;
use KeyHoang\OrgModule\Events\SyncUserEvent;
use KeyHoang\OrgModule\Listeners\SyncDepartmentListener;
use KeyHoang\OrgModule\Listeners\SyncUserListener;

class EventServiceProvider extends ServiceProvider
{
    protected $listen
        = [
            // SyncUserEvent::class       => [
            //     SyncUserListener::class,
            // ],
            SyncDepartmentEvent::class => [
                SyncDepartmentListener::class
            ]
        ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
