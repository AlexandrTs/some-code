<?php

namespace App\Rules;

use App\Contracts\CsCartOrderInterface;
use App\Repositories\CsCartOrderRepository;
use Illuminate\Contracts\Validation\Rule;

class OrderAmountRule implements Rule
{
    private $order_repo;
    private $order_amount;
    private $requested_amount;

    public function __construct(CsCartOrderInterface $repo)
    {
        $this->order_repo = $repo;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $order_id = request()->input('order_id');
        $order = $this->order_repo->getByOrderId($order_id);
        if($order && floatval($value) > floatval($order->total)){
            $this->order_amount = $order->total;
            $this->requested_amount = $value;
            return false;
        }
        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return sprintf(
            'New order amount must be lower than current amount. Requested amount: %s, current amount: %s',
            $this->requested_amount,
            $this->order_amount
        );
    }
}
