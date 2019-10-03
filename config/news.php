<?php

return [

    'news_category_id' => 7,

    'router' => [
        'prefix' => '/news',
        'as' => 'forum.',
        'api_prefix' => 'api',
        'thread_prefix' => 't',
        'category_prefix' => 'c',
        'namespace' => '\Riari\Forum\Http\Controllers\Frontend',
        'middleware' => ['web','auth']
    ],

];
