<?php

namespace App\Services\API;

use App\Exceptions\InvalidConfigurationException;
use App\Facades\CsEvent;
use Illuminate\Http\Client\Factory;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Request;
use Illuminate\Http\Client\Response;
use App\Contracts\APILoggerInterface;
use App\Contracts\APIServiceLoggerInterface;
use Illuminate\Support\Collection;


class APIService extends PendingRequest implements APIServiceLoggerInterface {

    /**
     * Request data
     *
     * @var array
     */
    private $last_request = [];

    /**
     * Eluminate response object
     *
     * @var \Illuminate\Http\Client\Response
     */
    private $response;

    /**
     * @var APILoggerInterface
     */
    private $logger;

    /**
     * @var array required configuration params
     */
    protected $config = [];

    /**
     * @var array required configuration parameters list (for check before request)
     */
    private $config_params = [];

    private $before_request = [];

    const PARAM_TYPE_DEFAULT = 0;
    const PARAM_TYPE_BASE_URL = 1;
    const PARAM_TYPE_URL = 2;

    const DEFAULT_TIMEOUT = 5;

    public function __construct(APILoggerInterface $logger)
    {
        $this->logger = $logger;
        parent::__construct();
    }

    public function post($url, $data = [])
    {
        return $this->execute('post', $url, $data);
    }

    public function get($url, $query = [])
    {
        return $this->execute('get', $url, $query);
    }

    public function put($url, $data = [])
    {
        return $this->execute('out', $url, $data);
    }

    public function execute($method, $url, $data = [])
    {
        $this->last_request = [
            'method' => strtoupper($method),
            'params' => null,
            'url' => null,
        ];

        $this->checkConfigParams();
        $this->executeBeforeRequest($url, $data);

        $this->last_request['params'] = $data;

        try {

            $that = $this;
            $this->beforeSending(function(Request $request) use ($that){
                $that->last_request['url'] = $request->url();
            });

            if(!isset($this->options['timeout'])) {
                $this->timeout(self::DEFAULT_TIMEOUT);
            }

            $this->response = parent::$method($url, $data);
            $this->last_request['status_code'] = $this->response->status();

            $response_body = $this->response->body();
            if (mb_strlen($response_body) > 3000) {
                $response_body = mb_substr($response_body, 0, 2000) . ' ------ content trucated ------';
            }

            $this->last_request['response'] = $response_body;
            if($this->response->failed()){
                $this->response->throw();
            }

        }catch(\Exception $e){
            $this->last_request['error'] = $e->getMessage();
        }

        $this->last_request['headers'] = $options['headers'] ?? [];

        if($this->logger instanceof APILoggerInterface){
            try {
                $this->logger->log($this->last_request, get_class($this));
            }catch(\Exception $e){
                // silently ignore
            }
        }

        return $this->response;
    }

    /**
     * Parse response xml to array
     *
     * @return array|null
     */
    protected function bodyXmlAsArray()
    {
        try {
            $xml = simplexml_load_string($this->response->body());
            $json = json_encode($xml);
            return json_decode($json, true);
        }catch(\Exception $e){
            return null;
        }
    }

    /**
     * Parse response xml to array
     *
     * @return array|null
     */
    protected function bodyJsonAsArray()
    {
        try {
            return json_decode($this->response->body(), true);
        }catch(\Exception $e){
            return null;
        }
    }

    /**
     * Return response and error as array
     *
     * @param bool $body_decode decode response body based on content-type header
     * @return array
     */
    public function responseOrError($body_decode = true)
    {
        $contents = $this->response ? $this->response->body() : null;
        $original_contents = $contents;
        $error = $this->last_request['error'] ?? null;
        $status_code = $this->response ? $this->response->status() : 500;
        if($body_decode && $this->response) {
            $content_type = $this->response->header('Content-Type');
            list($content_type, ) = explode(';', $content_type);
            $contents = match (trim($content_type)) {
                'application/json' => $this->bodyJsonAsArray(),
                'text/xml', 'application/xml' => $this->bodyXmlAsArray(),
                default => $contents,
            };
            if($original_contents === $contents && !isset($this->last_request['error'])){
                $status_code = 500;
                $error = "Incorrect response";
            }
        }
        return [
            'response' => $contents,
            'error' => $error,
            'status_code' => $status_code,
        ];
    }

    /**
     * Return last request and response data
     *
     * @param bool $with_response if false only request data will be returned
     * @return array
     */
    public function getLastRequestData($with_response = true)
    {
        $out = $this->last_request;
        if(!$out){
            return $out;
        }
        if(!$with_response){
            $out = [
                'url' => $out['url'],
                'params' => $out['params']
            ];
        }
        return $out;
    }

    /**
     * Logger object with log method
     *
     * @param $logger
     */
    public function setLogger(APILoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function setConfigParam($param_name, $config_var_name, $type = self::PARAM_TYPE_DEFAULT)
    {
        $this->config_params[$param_name] = [
            'config_var_name' => $config_var_name,
            'type' => $type
        ];
        return $this;
    }

    public function checkConfigParams()
    {
        if(!$this->config_params){
            return true;
        }
        foreach($this->config_params as $param_name => $param_info){
            $type = $param_info['type'];
            $config_var_name = $param_info['config_var_name'];
            $value = config($config_var_name);
            if(!$value){
                throw new InvalidConfigurationException(sprintf(
                    'Required configuration parameter missing: %s',
                    $config_var_name
                ));
                return false;
            }
            switch($type){
                case self::PARAM_TYPE_BASE_URL:
                    $this->baseUrl($value);
                    break;
                default:
                    $this->config[$param_name] = $value;
            }
        }
        return true;
    }

    public function beforeRequest($callback)
    {
        $this->before_request[] = $callback;
        return $this;
    }

    public function executeBeforeRequest(&$url, &$data)
    {
        if($this->before_request){
            foreach($this->before_request as $callback){
                call_user_func($callback, $url, $data);
            }
        }
    }

}
