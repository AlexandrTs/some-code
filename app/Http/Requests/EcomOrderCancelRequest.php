<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\OrderExistsRule;

class EcomOrderCancelRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true; 
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'order_id' => ['bail', 'required', 'integer', resolve(OrderExistsRule::class)],
            'salt' => ['sometimes', 'min:6'],
            'ts' => ['sometimes', 'integer', 'min:10'],
        ];
    }
}
