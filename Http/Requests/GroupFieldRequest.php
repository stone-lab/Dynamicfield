<?php

namespace Modules\Dynamicfield\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GroupFieldRequest extends FormRequest
{
    /**
     * @return array
     */
    public function rules()
    {
        return [
            'group.name' => 'required',
        ];
    }

    /**
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * @return array
     */
    public function messages()
    {
        return [
            'group.name.required' => trans('dynamicfield::messages.group.name required'),
        ];
    }
}
