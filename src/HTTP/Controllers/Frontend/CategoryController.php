<?php namespace Riari\Forum\Http\Controllers\Frontend;

use Forum;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Illuminate\Support\Facades\Gate;
use Riari\Forum\Events\UserViewingCategory;
use Riari\Forum\Events\UserViewingIndex;
use Riari\Forum\Http\Requests\StoreCategory;

use Riari\Forum\Models\Category;
use Riari\Forum\Models\Post;
use Riari\Forum\Models\Thread;

class CategoryController extends BaseController
{
    protected $categories;
    protected $threads;
    protected $posts;

    protected function CategoryModel()
    {
        return new Category;
    }

    protected function ThreadModel()
    {
        return new Thread;
    }

    protected function PostModel()
    {
        return new Post;
    }

    private function sub_array(array $haystack, array $needle)
    {
        return array_intersect_key($haystack, array_flip($needle));
    }

    public function index(Request $request): View
    {
        $newsid = config('forum.frontend.news_category_id');
        $categories = $this->CategoryModel()->where("id", "!=", $newsid)->get()->sortBy("weight");
        event(new UserViewingIndex);
        return view('forum::category.index', compact('categories'));
    }

    public function show(Request $request): View
    {
        $category = $this->CategoryModel()->find($request->category);
        event(new UserViewingCategory($category));

        $categories = [];
        if (Gate::allows('moveCategories')) {
            $categories = $this->CategoryModel()->all();
        }

        $threads = $category->threadsPaginated;
        return view('forum::category.show', compact('categories', 'category', 'threads'));
    }

    public function store(StoreCategory $request): RedirectResponse
    {
         $this->validate($request, [
             'title'             => ['required']
         ]);

        $this->authorize('createCategories');

        $parameters = [
            'title'         => $request->input('title'),
            'description'       => $request->input('description'),
            'accepts_threads'       => $request->input('accepts_threads'),
            'is_private'       => $request->input('private'),
            'color'       => $request->input('color')
        ];

        $category = $this->CategoryModel()->create($parameters);

        Forum::alert('success', 'categories.created');

        return redirect(Forum::route('category.show', $category));
    }

    public function update(Request $request): RedirectResponse
    {
        $action = $request->input('action');
        $id = $request->category;

        if ($action == "rename")
        {
             $this->authorize('renameCategories');
             $this->validate($request, ['title' => ['required']]);
             $category = $this->CategoryModel()->find($id);
             $this->updateModel($category, $request->only(['title', 'description']));
        }
        if ($action == "move")
        {
             $this->authorize('moveCategories');
             $this->validate($request, ['category_id' => ['required']]);
             $category = $this->CategoryModel()->find($id);
             $this->updateModel($category, ['category_id' => $request->input('category_id')]);
        }
        if ($action == "reorder")
        {
             $this->authorize('moveCategories');
             $this->validate($request, ['weight' => ['required']]);
             $category = $this->CategoryModel()->find($id);
             $this->updateModel($category, ['weight' => $request->input('weight')]);
        }
        if ($action == "makeprivate")
        {
             $this->authorize('createCategories');
             $category = $this->CategoryModel()->where('private', 0)->find($id);
             $this->updateModel($category, ['private' => 1]);
        }
        if ($action == "makepublic")
        {
             $this->authorize('createCategories');
             $category = $this->CategoryModel()->where('private', 1)->find($id);
             $this->updateModel($category, ['private' => 0]);
        }
        if ($action == "disablethreads")
        {
             $category = $this->CategoryModel()->where('enable_threads', 1)->find($id);
             if (!$category->threads->isEmpty()) {
                 return $this->buildFailedValidationResponse($request, trans('forum::validation.category_has_no_threads'));
             }
             $this->updateModel($category, ['enable_threads' => 0], 'enableThreads');
        }
        if ($action == "enablethreads")
        {
             $category = $this->CategoryModel()->where('enable_threads', 0)->find($id);
             return $this->updateModel($category, ['enable_threads' => 1], 'enableThreads');
        }

        Forum::alert('success', 'categories.updated', 1);
        return redirect(Forum::route('category.show', $category));
    }

    public function destroy(Request $request): RedirectResponse
    {
        $this->CategoryModel()->find($request->category)->delete();

        Forum::alert('success', 'categories.deleted', 1);

        return redirect(config('forum.frontend.router.prefix'));
    }
}
