<?php

namespace App\Http\Requests;

use App\Repositories\CsCartOrderRepository;
use App\Rules\OrderAmountRule;
use App\Rules\OrderExistsRule;
use Illuminate\Foundation\Http\FormRequest;

class EcomOrderAmountRequest extends FormRequest
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
            'amount' => ['bail', 'required', 'numeric', resolve(OrderAmountRule::class)],
            'salt' => ['sometimes', 'min:6'],
            'ts' => ['sometimes', 'integer', 'min:10'],
        ];
    }

}
