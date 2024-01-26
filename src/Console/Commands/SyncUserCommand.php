<?php

namespace KeyHoang\OrgModule\Console\Commands;

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
        $url = config('organization.organization_url') . "/public/api/public/users";
        $this->sync(SyncUserEvent::class, $url, 'user');
    }
}
