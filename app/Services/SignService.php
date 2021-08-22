<?php
declare(strict_types=1);

namespace App\Services;

class SignService {

    /**
     * Query string params
     * @var array
     */
    private $query_params;

    /**
     * Shared API key for sign
     * @var string
     */
    private $shared_key;

    /**
     * Last error message
     * @var string
     */
    private $last_error;

    public function __construct(string $shared_key)
    {
        $this->shared_key = $shared_key;
    }

    private function getSign(array $params)
    {
        // ...
    }

    public function checkSign($params): bool
    {
        $required = ['ts', 'salt', 'sign'];
        foreach ($required as $param) {
            if (!isset($params[$param])) {
                $this->last_error = sprintf('Required parameter missing: %s', $param);
                return false;
            }
        }
        $sign = $params['sign'];
        unset($params['sign']);
        $result = $this->getSign($params) === $sign;
        if(!$result){
            $this->last_error = 'Invalid sign';
        }
        if(isset($params['ttl'])){
            if(time() - $params['ts'] > $params['ttl']){
                $this->last_error = 'Request expired';
                return false;
            }
        }
        return $result;
    }

    public function getLastError()
    {
        return $this->last_error;
    }
}
