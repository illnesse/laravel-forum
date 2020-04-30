<?php namespace Riari\Forum\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Riari\Forum\Models\Traits\HasAuthor;
use Riari\Forum\Support\Traits\CachesData;

use Riari\Forum\Support\Frontend\Forum;
use Spatie\Searchable\Searchable;
use Spatie\Searchable\SearchResult;

class Post extends BaseModel implements Searchable
{
    use SoftDeletes, HasAuthor, CachesData;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'forum_posts';

    /**
     * @var array
     */
    protected $dates = ['deleted_at'];

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
    protected $fillable = ['thread_id', 'author_id', 'post_id', 'content'];

    /**
     * Create a new post model instance.
     *
     * @param  array  $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->setPerPage(config('forum.general.pagination.posts'));
    }

    /**
     * Relationship: Thread.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function thread()
    {
        return $this->belongsTo(Thread::class)->withTrashed();
    }

    public function category()
    {
        return $this->thread->belongsTo(Category::class);
    }

    /**
     * Relationship: Parent post.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent()
    {
        return $this->belongsTo(Post::class, 'post_id');
    }

    /**
     * Relationship: Child posts.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function children()
    {
        return $this->hasMany(Post::class, 'post_id')->withTrashed();
    }

    /**
     * Attribute: First post flag.
     *
     * @return boolean
     */
    public function getIsFirstAttribute()
    {
        return $this->id == $this->thread->firstPost->id;
    }

    /**
     * Helper: Sequence number in thread.
     *
     * @return int
     */
    public function getSequenceNumber()
    {
        foreach ($this->thread->posts as $index => $post) {
            if ($post->id == $this->id) {
                return $index + 1;
            }
        }
    }

    public function getSearchResult(): SearchResult
    {
        return new \Spatie\Searchable\SearchResult(
            $this,
            $this->content,
            Forum::route('post.show', $this)
        );
    }

}
