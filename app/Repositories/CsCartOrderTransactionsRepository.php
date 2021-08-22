<?php

namespace App\Repositories;

use App\Contracts\CsCartOrderInterface;
use App\Models\CsCartOrder;
use App\Models\CsCartOrderTransactions;
use App\Models\EcomOrderStatusRequest;
use Illuminate\Support\Facades\DB;

class CsCartOrderTransactionsRepository implements CsCartOrderInterface
{
    public function getByOrderId($order_id)
    {
        return CsCartOrderTransactions::query()
            ->select('order')
            ->where('order_id', '=', $order_id)
            ->first();
    }
}



















