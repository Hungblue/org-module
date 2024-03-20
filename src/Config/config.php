<?php

return [
    'name'                       => 'Organization',
    'organization_url'           => env("ORGANIZATION_URL"),
    'organization_grant_type'    => env("ORGANIZATION_GRANT_TYPE"),
    'organization_client_id'     => env("ORGANIZATION_CLIENT_ID"),
    'organization_client_secret' => env('ORGANIZATION_CLIENT_SECRET'),
    'organization_sync_limit'    => env('ORGANIZATION_SYNC_LIMIT', 100),
    'set_role'                   => env('SET_ROLE', false),
];
