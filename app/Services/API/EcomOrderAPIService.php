<?php
declare(strict_types=1);

namespace App\Services\API;

use App\Contracts\APILoggerInterface;
use App\Services\RequestResponseAPILoggerService;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Request;
use App\Services\API\APIService;

/**
 * Class EcomOrderAPIService
 *
 * http://conf.corp..kz/pages/viewpage.action?pageId=18678834
 */
class EcomOrderAPIService
{
    /**
     * @var string[] list of required parameters
     */
    private $required_params = ['ORDER', 'MERCHANT'];

    /**
     * @var merchant secret key
     */
    private $secret_key;

    /**
     * @var array params for sign
     */
    private $sign_params = [];

    private $api;

    public function setSecretKey($secret_key)
    {
        $this->secret_key = $secret_key;
        return $this;
    }

    public function setSignParams($sign_params)
    {
        $this->sign_params = $sign_params;
        return $this;
    }

    public function sign($params, $trailing_semicolon = true)
    {
        if($this->sign_params) {
            // ...
            $params['SIGN'] = '// ...';
        }
        return $params;
    }

    public function post($params)
    {
        $params = $this->sign($params, !isset($params['GETSTATUS']));
        $this->api = new APIService(new RequestResponseAPILoggerService());
        $this->api
            ->setConfigParam( 'url', 'api.ecom_url')
            ->asForm()
            ->post(config('api.ecom_url'), $params);

        return $this->response();
    }

    public function getLastRequestData($with_body = false)
    {
        return $this->api->getLastRequestData($with_body);
    }

    private function response()
    {
        $result = $this->api->responseOrError();
        $error = false;
        // ...
    }

}
