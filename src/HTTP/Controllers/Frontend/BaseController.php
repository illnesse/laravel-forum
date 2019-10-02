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
    use AuthorizesRequests, ValidatesRequests;
    /**
     * Handle a response from the dispatcher for the given request.
     *
     * @param  Request  $request
     * @param  Response  $response
     * @return Response|mixed
     */
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

    protected function updateModel($model, array $attributes, $authorize = [])
    {
        if (is_null($model) || !$model->exists) {
            return $this->notFoundResponse();
        }

        $this->parseAuthorization($model, $authorize);

        $model->update($attributes);

        return $this->response($model, 'updated');
    }

    protected function parseAuthorization($model, $authorize = [])
    {
        if (!empty($authorize)) {
            // We need to authorize this change

            if (is_string($authorize)) {
                // Only an ability name was given, so use $model
                $authorize = [$authorize, $model];
            }

            list($ability, $authorizeModel) = $authorize;

            $this->authorize($ability, $authorizeModel);
        }
    }

    protected function response($data, $message = "", $code = 200)
    {
        $message = empty($message) ? [] : compact('message');

        return (request()->ajax() || request()->wantsJson())
            ? new JsonResponse($message + compact('data'), $code)
            : new Response($data, $code);
    }
}
