<?php
declare(strict_types=1);

namespace App\Services;

use App\Facades\EcomOrderAPI;
use App\Http\API\v1\EcomOrder;
use App\Repositories\CsCartOrderRepository;
use App\Repositories\CsCartOrderTransactionsRepository;
use App\Services\API\EcomOrderAPIService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

final class EcomOrderService {

    private $orders_repo;

    private $payment;

    private $order_transactions_repo;

    public $api;

    public function __construct(
        CsCartOrderRepository $orders_repository,
        PaymentService $payment,
        CsCartOrderTransactionsRepository $order_transactions_repository,
        EcomOrderAPIService $ecom_api
    )
    {
        $this->orders_repo = $orders_repository;
        $this->payment = $payment;
        $this->order_transactions_repo = $order_transactions_repository;
        $this->api = $ecom_api;
    }


    public function amount($order_id, $amount)
    {
        return $this->makeAmountRequest($order_id, $amount);
    }

    public function cancel($order_id)
    {
        return $this->makeAmountRequest(order_id: $order_id, full_amount: true);
    }

    /**
     * @param $order_id
     * @param int $amount
     * @param false $full_amount
     * @return array ['response', 'status_code']
     */
    public function makeAmountRequest($order_id, $amount = 0, $full_amount = false)
    {
        $out = [
            'message' => null,
            'response' => null,
            'status' => 'success',
            'status_code' => 200,
        ];
        try {
            // get order info from cscart table
            $order = $this->orders_repo->getByOrderId($order_id);
            if (!$order) {
                throw new \Exception(sprintf('Order with id: %d not found', $order_id));
                return;
            }
            if ($full_amount) {
                $amount = $order->total;
            } else {
                if ($order->total < $amount) {
                    throw new \Exception(sprintf(
                        'Requested amount: %s more than holded amount: %s',
                        $amount,
                        $order->amount
                    ));
                    return;
                }
            }
            $info = $this->getRequiredOrderInfo($order);
            $formatted_amount = $this->formatAmount($amount);
            $params = [
                'ORDER' => $info['ecom_order_id'],
                'MERCHANT' => $info['merchant_id'],
                'REV_AMOUNT' => $formatted_amount,
                'REV_DESC' => $full_amount ? 'Order cancelled by JPost' : 'Order amount changed by JPost',
                'LANGUAGE' => 'en',
            ];

            // make request to jysan to change order amount
            $this->api = EcomOrderAPI::setSecretKey($info['secret_key']);
            $result = $this->api
                ->setSignParams(['ORDER', 'MERCHANT', 'REV_AMOUNT', 'REV_DESC'])
                ->post($params);

            $out = array_merge($out, $result);

        }catch(\Exception $e){
            $out['message'] = $e->getMessage();
            $out['status'] = 'error';
            $out['status_code'] = 400;
            Log::warning('Request error', ['message' => $e->getMessage(), 'trace' => $e->getTrace()]);
        }
        return $out;
    }

    public function withdrawal($order_id)
    {
        $out = [
            'message' => null,
            'response' => null,
            'status' => 'success',
            'status_code' => 200,
        ];
        try {
            // get order info from cscart table
            $order = $this->orders_repo->getByOrderId($order_id);
            if (!$order) {
                throw new \Exception(sprintf('Order with id: %d not found', $order_id));
                return;
            }
            $info = $this->getRequiredOrderInfo($order);
            $params = [
                'APPROVE' => 'Y',
                'ORDER' => $info['ecom_order_id'],
                'MERCHANT' => $info['merchant_id'],
                'LANGUAGE' => 'en',
            ];
            // make request to jysan to withdrawal order
            $this->api = EcomOrderAPI::setSecretKey($info['secret_key']);
            $result = $this->api
                ->setSignParams(['ORDER', 'MERCHANT'])
                ->post($params);

            $out = array_merge($out, $result);

        }catch(\Exception $e){
            $out['message'] = $e->getMessage();
            $out['status'] = 'error';
            $out['status_code'] = 400;
            Log::warning('Request error', ['message' => $e->getMessage(), 'trace' => $e->getTrace()]);
        }
        return $out;
    }

    public function status($order_id)
    {
        $out = [
            'error' => null,
            'response' => null,
            'status' => 'success',
            'status_code' => 200,
        ];
        try {
            // get order info from cscart table
            $order = $this->orders_repo->getByOrderId($order_id);
            if (!$order) {
                throw new \Exception(sprintf('Order with id: %d not found', $order_id));
                return;
            }
            $info = $this->getRequiredOrderInfo($order);
            $params = [
                'ORDER' => $info['ecom_order_id'],
                'MERCHANT' => $info['merchant_id'],
                'GETSTATUS' => '1',
                'LANGUAGE' => 'en',
            ];

            $this->api = EcomOrderAPI::setSecretKey($info['secret_key']);
            $result = $this->api
                ->setSignParams(['ORDER', 'MERCHANT'])
                ->post($params);

            $out = array_merge($out, $result);

        }catch(\Exception $e){
            $out['error'] = $e->getMessage();
            $out['status'] = 'error';
            $out['status_code'] = 400;
            Log::warning('Request error', ['message' => $e->getMessage(), 'trace' => $e->getTrace()]);
        }
        return $out;
    }

    private function formatAmount($amount)
    {
        return number_format(floatval($amount), 2, '.', '' );
    }

    private function getRequiredOrderInfo($order)
    {
        // get processor params
        $processor = $this->payment->getProcessorInfoByPaymentId($order->payment_id);
        if(!isset($processor['merchant_id']) || !$processor['merchant_id']){
            throw new \Exception(sprintf(
                'Merchant id not found. For order with id: %s',
                $order->order_id
            ));
        }
        // get ecom order id
        $ecom_order = $this->order_transactions_repo->getByOrderId($order->order_id);
        if(!$ecom_order){
            throw new \Exception(sprintf(
                'Ecom order transaction not found for order with id: %s',
                $order->order_id
            ));
            return;
        }
        if(!$ecom_order->order){
            throw new \Exception(sprintf('Ecom order id not found for order with id: %s', $order->order_id));
            return;
        }
        return [
            'merchant_id' => $processor['merchant_id'],
            'ecom_order_id' => $ecom_order->order,
            'secret_key' => $processor['secret_key'],
        ];
    }

}
