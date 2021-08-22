<?php

namespace App\Repositories;

use App\Contracts\CsCartOrderInterface;
use App\Models\CsCartOrder;
use App\Models\EcomOrderStatusRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CsCartOrderRepository implements CsCartOrderInterface
{
    private $data;

    /**
     * Return list of orders without crystal_payout_log
     *
     * @param int $order_timeout minutes
     * @return mixed
     */
    public function getOrdersWithoutPayout($order_timeout = 60)
    {
        $list = DB::connection('cscart')
            ->table('order_transactions')
            ->select(
                'order_transactions.order',
                'orders.order_id',
                'orders.payment_id',
                'orders.lang_code'
            )
            ->leftJoin(
                'orders',
                'orders.order_id',
                '=',
                'order_transactions.order_id'
            )
            ->where('order_transactions.status', '=', 'D')
            ->where('orders.is_parent_order', '=', 'N')
            ->whereRaw(DB::raw(sprintf("order_transactions.created_at BETWEEN
                (CURRENT_TIMESTAMP() - INTERVAL  23 DAY) AND (CURRENT_TIMESTAMP() - INTERVAL %d MINUTE)",
                $order_timeout
            )))
            ->whereNull('order_transactions.ecomId')
            ->limit(1500)
            ->orderBy('order_transactions.order_id', 'DESC')
            ->get()
            ->toArray();

        $skip_orders = [];
        if($list){
            $orders = array_column($list, 'order_id');
            $skip_orders = EcomOrderStatusRequest::select('order_id')
                ->whereIn('order_id', $orders)
                ->get()
                ->toArray();
            if($skip_orders){
                $skip_orders = array_column($skip_orders, 'order_id');
                foreach($list as $order_key => $order){
                    if(in_array($order->order_id, $skip_orders)){
                        unset($list[$order_key]);
                    }
                }
            }
        }
        return $list ?? [];
    }

    public function getByOrderId($order_id)
    {
        if(isset($this->data[$order_id])){
            return $this->data[$order_id];
        }
        $this->data[$order_id] = CsCartOrder::query()
            ->select('*')
            ->where('order_id', '=', $order_id)
            ->first();
        return $this->data[$order_id];
    }
}



















