<?php
declare(strict_types=1);

namespace App\Services\API;

use App\Contracts\APILoggerInterface;
use App\Services\RequestResponseAPILoggerService;

/**
 * Запросы на API loans: jmart/api/v1/applications/product-loans/
 *
 * Class JysanLoansService
 * @package App\Services
 */
class JysanLoansAPIService {

    private $api;

    private function prepare()
    {
        $this->api = new APIService(new RequestResponseAPILoggerService());
        $this->api
            ->withoutVerifying()
            ->setConfigParam('username', 'api.loans_auth_user')
            ->setConfigParam('password', 'api.loans_auth_password')
            ->setConfigParam('url', 'api.loans_base_url')
            ->baseUrl(config('api.loans_base_url'));
    }

    public function post($url, $params)
    {
        $this->prepare();
        $this->api
            ->asForm()
            ->post($url, $params);

        return $this->response();
    }

    public function get($url, $params)
    {
        $this->prepare();
        $this->api->get($url, $params);

        return $this->response();

    }

    public function response()
    {
        $result = $this->api->responseOrError();
        if(isset($result['response']['code'])){
            $result['status_code'] = $result['response']['code'];
        }
        return $result;
    }

}
