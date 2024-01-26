<?php

namespace KeyHoang\OrgModule\Console\Commands;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use KeyHoang\OrgModule\Events\SyncDepartmentEvent;

class SyncDepartmentCommand extends CommandBase
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'department:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync department.';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $url = config('organization.organization_url') . "/public/api/public/departments";
        $this->sync(SyncDepartmentEvent::class, $url, 'department');
    }
}
