<?php

return [
    'commentables' => [
        \App\Models\Tenant\Comment::class,
        \App\Models\Tenant\Task::class,
    ],

    'taggables' => [
        \App\Models\Tenant\Task::class,
    ],

    'eventables' => [
        \App\Models\Tenant\Campaign::class,
    ],
];