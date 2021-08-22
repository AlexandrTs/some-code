<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Requests\ProductLoansStateRequest;
use App\Http\Controllers\LoansStatusChecker;
use App\Http\Controllers\Healthcheck;
use App\Http\API\v1\EcomOrder;
use App\Http\API\v1\ProductLoans;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/', function (Request $request) {
    return "OK";
});

/**
 * Healthchecks for users and kubernetes
 */
Route::get('/healthcheck', [Healthcheck::class, 'healthcheck']);
Route::get('/liveness', [Healthcheck::class, 'liveness']);
Route::get('/readiness', [Healthcheck::class, 'readiness']);

/**
 * Работа с кредитами и рассрочками
 */
Route::get('/v1/loans/state', [ProductLoans::class, 'state']);

/**
 * Работа с заказами (без кредитов и рассрочек)
 */
Route::put('/v1/orders/amount', [EcomOrder::class, 'amount']);
Route::put('/v1/orders/withdrawal', [EcomOrder::class, 'withdrawal']);
Route::post('/v1/orders/cancel', [EcomOrder::class, 'cancel']);




