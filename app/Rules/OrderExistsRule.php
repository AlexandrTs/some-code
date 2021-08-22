<?php

namespace App\Rules;

use App\Contracts\CsCartOrderInterface;
use App\Repositories\CsCartOrderRepository;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\MessageBag;

class OrderExistsRule implements Rule
{
    private $order_repo;
    private $value;

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
        $order = $this->order_repo->getByOrderId($value);
        $this->value = floatval($value);
        if(!$order){
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
        return sprintf('Order with id: %s not found', $this->value);
    }
}
