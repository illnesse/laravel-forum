<?php

return [

    'news_category_id' => 7,

    'router' => [
        'prefix' => '/news',
        'as' => 'news.',
        'api_prefix' => 'api',
        'thread_prefix' => 'nt',
        'category_prefix' => 'nc',
        'namespace' => '\Riari\Forum\Http\Controllers\Frontend',
        'middleware' => ['web','auth']
    ],

];
