<?php

namespace Riari\Forum\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreThread extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('createThreads');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'author_id' => ['required', 'integer'],
            'title'     => ['required'],
            'content'   => ['required']
        ];
    }
}
