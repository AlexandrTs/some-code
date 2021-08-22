<?php
declare(strict_types=1);

namespace App\Services;

use App\Contracts\APILoggerInterface;
use App\Models\APIRequestLog;

class RequestResponseAPILoggerService implements APILoggerInterface {


    /**
     * Save log to database
     *
     * @param array $response
     * @param string $executor
     * @return bool
     */
    public static function log(array $response, string $executor): bool
    {
        $fields = [
            'request_id',
            'method',
            'url',
            'params',
            'headers',
            'status_code',
            'error',
            'response',
            'incoming',
        ];
        $log = new APIRequestLog();
        foreach($fields as $field){
            if(isset($response[$field]) && $response[$field]) {
                $log->{$field} = is_array($response[$field])
                    ? json_encode($response[$field], JSON_UNESCAPED_UNICODE)
                    : $response[$field];
            }
        }
        $log->request_id = request()->headers->get('X-Request-ID');
        $log->executor = $executor;
        $log->ts = date('Y-m-d H:i:s', time());
        return $log->save();
    }

}
