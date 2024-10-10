## Installation
You can install this package via composer using this command:

```
composer require thadico-platform/org-module
```
Register event providers:

```
\KeyHoang\OrgModule\Providers\EventServiceProvider::class
```

Using this schedule:

```
$schedule->command(SyncUserCommand::class)->daily();
$schedule->command(SyncDepartmentCommand::class)->daily();
```

Using this RabbitMQJob:

```
'sync-user' => SyncUserQueue::class . '@handle'
```