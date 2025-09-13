<?php

use RickDBCN\FilamentEmail\Filament\Resources\EmailResource;
use RickDBCN\FilamentEmail\Models\Email;

return [

    'resource' => [
        'class' => EmailResource::class,
        'model' => Email::class,
        'cluster' => null,
        'group' => 'System',
        'sort' => null,
        'icon' => false,
        'default_sort_column' => 'created_at',
        'default_sort_direction' => 'desc',
        'datetime_format' => 'Y-m-d H:i:s',
        'table_search_fields' => [
            'subject',
            'from',
            'to',
            'cc',
            'bcc',
        ],
        'has_title_case_model_label' => false,
    ],

    'keep_email_for_days' => 90,

    'label' => 'Sent Emails',

    'prune_enabled' => true,

    'prune_crontab' => '0 0 * * *',

    'can_access' => [
        'role' => ['super_admin', 'admin', 'PMT'],
    ],

    'pagination_page_options' => [
        50,
        100,
        250,
        500,
    ],

    'attachments_disk' => 'local',
    'store_attachments' => false,

    // Use this option for customize tenant model class
    // 'tenant_model' => \App\Models\Team::class,

];
