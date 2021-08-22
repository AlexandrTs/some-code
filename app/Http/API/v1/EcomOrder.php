<?php

namespace App\Http\API\v1;

use App\Http\Requests\EcomOrderAmountRequest;
use App\Http\Requests\EcomOrderWithdrawalRequest;
use App\Http\Requests\EcomOrderCancelRequest;
use App\Services\EcomOrderService;
use App\Http\Controllers\Controller;

/**
 * @OA\Info(title="JysanPaymentSrv Order API", version="1.0")
 */
class  EcomOrder extends Controller
{
    private $ecom_service;

    public function __construct(EcomOrderService $ecom_service)
    {
        $this->ecom_service = $ecom_service;
    }

    /**
     * @OA\Put(
     *     path="/jysan-payment/api/v1/orders/amount",
     *     summary="Update order amount",
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="order_id",
     *                     type="integer"
     *                 ),
     *                 @OA\Property(
     *                     property="amount",
     *                     type="string"
     *                 ),
     *                 example={"order_id": 103489, "amount": "107000.00", "salt": 232342, "ts": 161000234234, "sign": "323..."}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Order amount changed successfully",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *         )
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Invalid payload. Order not found or something similar",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *         )
     *     ),
     *     @OA\Response(
     *         response="422",
     *         description="Invalid arguments",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *         )
     *     ),
     *     @OA\Response(
     *         response="500",
     *         description="Internal server or external api request error",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *         )
     *     )
     * )
     */
    public function amount(EcomOrderAmountRequest $request)
    {
        if(intval($request->amount) == 0){
            $out = $this->ecom_service->withdrawal($request->order_id);
        }else{
            $out = $this->ecom_service->amount($request->order_id, $request->amount);
        }

        return response($out, $out['status_code']);
    }

    /**
     * @OA\Put(
     *     path="/jysan-payment/api/v1/orders/withdrawal",
     *     summary="Dehold order and withdraw money from customer card",
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="order_id",
     *                     type="integer"
     *                 ),
     *                 example={"order_id": 103489, "salt": 232342, "ts": 161000234234, "sign": "323..."}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Order deholded and money withdrawed",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *         )
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Invalid payload. Order not found or something similar",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *         )
     *     ),
     *     @OA\Response(
     *         response="422",
     *         description="Invalid arguments",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *         )
     *     ),
     *     @OA\Response(
     *         response="500",
     *         description="Internal server or external api request error",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *         )
     *     )
     * )
     */
    public function withdrawal(EcomOrderWithdrawalRequest $request)
    {
        $out = $this->ecom_service->withdrawal($request->order_id);
        return response($out, $out['status_code']);
    }

    /**
     * @OA\Post(
     *     path="/jysan-payment/api/v1/orders/cancel",
     *     summary="Cancel order and return money to customer card",
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="order_id",
     *                     type="integer"
     *                 ),
     *                 example={"order_id": 103489, "salt": 232342, "ts": 161000234234, "sign": "323..."}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Order cancelled and money returned to customer",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *         )
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Invalid payload. Order not found or something similar",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *         )
     *     ),
     *     @OA\Response(
     *         response="422",
     *         description="Invalid arguments",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *         )
     *     ),
     *     @OA\Response(
     *         response="500",
     *         description="Internal server or external api request error",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *         )
     *     )
     * )
     */
    public function cancel(EcomOrderCancelRequest $request)
    {
        $out = $this->ecom_service->cancel($request->order_id);
        return response($out, $out['status_code']);
    }

}
