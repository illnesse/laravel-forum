<?php

namespace Riari\Forum\Services;

use Riari\Forum\Models\Thread;
use Riari\Forum\Models\Post;

class ThreadService
{
    /** @var Model */
    private $model;

    function sub_array(array $haystack, array $needle)
    {
        return array_intersect_key($haystack, array_flip($needle));
    }

    public function __construct()
    {
        $this->model = new Thread;
    }

    public function getAll()
    {
        return $this->model->all();
    }

    public function getTopLevel()
    {
        return $this->model->where('parent_id', 0)->get();
    }

    public function getByID(int $id)
    {
        return $this->model->findOrFail($id);
    }

    public function create($attributes)
    {
        $thread = $this->model->create($this->sub_array($attributes, ['category_id', 'author_id', 'title']));
        Post::create(['thread_id' => $thread->id] + $this->sub_array($attributes, ['author_id', 'content']));

        return $thread;
    }
}