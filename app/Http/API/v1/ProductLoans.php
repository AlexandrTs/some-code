<?php

namespace App\Http\API\v1;

use App\Http\Requests\ProductLoansApplyRequest;
use App\Http\Requests\ProductLoansStateRequest;
use App\Http\Requests\ProductLoansUpdateStateRequest;
use App\Services\APIService;
use App\Services\JysanLoansService;
use App\Http\Controllers\Controller;

// @TODO move to API/v1
class ProductLoans extends Controller
{
    private $loans;

    public function __construct(JysanLoansService $loans)
    {
        $this->loans = $loans;
    }

    public function state(ProductLoansStateRequest $request)
    {
        $out = $this->loans->getState($request->get('order_id'));
        return response($out, $out['status_code'] ?: 200);
    }
}
