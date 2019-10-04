<?php
$ns = config('forum.frontend.router.namespace');
$r->get('/', ['as' => 'index', 'uses' => $ns.'CategoryController@index']);
$r->get('new', ['as' => 'new.index', 'uses' => $ns.'ThreadController@indexNew']);
$r->patch('new', ['as' => 'new.mark-read', 'uses' => $ns.'ThreadController@markNewAsRead']);

$categoryPrefix = config('forum.frontend.router.category_prefix');
$r->post($categoryPrefix . '/create', ['as' => 'category.store', 'uses' => $ns.'CategoryController@store']);
$r->group(['prefix' => $categoryPrefix . '/{category}-{category_slug}'], function ($r)
{
    $ns = config('forum.frontend.router.namespace');
    $r->get('/', ['as' => 'category.show', 'uses' => $ns.'CategoryController@show']);
    $r->patch('/', ['as' => 'category.update', 'uses' => $ns.'CategoryController@update']);
    $r->delete('/', ['as' => 'category.delete', 'uses' => $ns.'CategoryController@destroy']);

    $r->get('t/create', ['as' => 'thread.create', 'uses' => $ns.'ThreadController@create']);
    $r->post('t/create', ['as' => 'thread.store', 'uses' => $ns.'ThreadController@store']);
});

$threadPrefix = config('forum.frontend.router.thread_prefix');
$r->group(['prefix' => $threadPrefix . '/{thread}-{thread_slug}'], function ($r)
{
    $ns = config('forum.frontend.router.namespace');
    $r->get('/', ['as' => 'thread.show', 'uses' => $ns.'ThreadController@show']);
    $r->patch('/', ['as' => 'thread.update', 'uses' => $ns.'ThreadController@update']);
    $r->delete('/', ['as' => 'thread.delete', 'uses' => $ns.'ThreadController@destroy']);

    $r->get('post/{post}', ['as' => 'post.show', 'uses' => $ns.'PostController@show']);
    $r->get('reply', ['as' => 'post.create', 'uses' => $ns.'PostController@create']);
    $r->post('reply', ['as' => 'post.store', 'uses' => $ns.'PostController@store']);
    $r->get('post/{post}/edit', ['as' => 'post.edit', 'uses' => $ns.'PostController@edit']);
    $r->patch('{post}', ['as' => 'post.update', 'uses' => $ns.'PostController@update']);
    $r->delete('{post}', ['as' => 'post.delete', 'uses' => $ns.'PostController@destroy']);
});

$r->group(['prefix' => 'bulk', 'as' => 'bulk.'], function ($r)
{
    $ns = config('forum.frontend.router.namespace');
    $r->patch('thread', ['as' => 'thread.update', 'uses' => $ns.'ThreadController@bulkUpdate']);
    $r->delete('thread', ['as' => 'thread.delete', 'uses' => $ns.'ThreadController@bulkDestroy']);
    $r->patch('post', ['as' => 'post.update', 'uses' => $ns.'PostController@bulkUpdate']);
    $r->delete('post', ['as' => 'post.delete', 'uses' => $ns.'PostController@bulkDestroy']);
});
