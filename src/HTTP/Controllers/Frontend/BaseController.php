<?php namespace Riari\Forum\Http\Controllers\Frontend;

use Forum;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Collection;
use Riari\Forum\API\Dispatcher;

abstract class BaseController extends Controller
{
    /*
    use AuthorizesRequests, ValidatesRequests;

    public function handleResponse(Request $request, Response $response)
    {
        if ($response->getStatusCode() == 422) {
            $errors = $response->getOriginalContent()['validation_errors'];

            throw new HttpResponseException(
                redirect()->back()->withInput($request->input())->withErrors($errors)
            );
        }

        if ($response->getStatusCode() == 403) {
            abort(403);
        }

        return $response->isNotFound() ? abort(404) : $response->getOriginalContent();
    }

    protected function bulkActionResponse(Collection $models, $transKey)
    {
        if ($models->count()) {
            Forum::alert('success', $transKey, $models->count());
        } else {
            Forum::alert('warning', 'general.invalid_selection');
        }

        return redirect()->back();
    }
    */
}
