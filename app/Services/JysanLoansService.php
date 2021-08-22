<?php
declare(strict_types=1);

namespace App\Services;

use App\Facades\JysanLoansAPI;
use App\Services\API\JmartSraAPIService;
use App\Services\API\JysanLoansAPIService;

final class JysanLoansService {

    /**
     * Get last loans request status
     *
     * @param $order_id
     * @return array|null
     */
    public function getState($order_id)
    {
        $result = JysanLoansAPI::get(
            '/api/v1/applications/product-loans/get-state',
            ['orderId' => $order_id]
        );

        return $result;
    }

}
