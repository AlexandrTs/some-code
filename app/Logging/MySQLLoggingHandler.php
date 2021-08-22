<?php
namespace App\Logging;

use DB;
use Monolog\Logger;
use Monolog\Handler\AbstractProcessingHandler;

class MySQLLoggingHandler extends AbstractProcessingHandler {

    public function __construct($level = Logger::DEBUG, $bubble = true)
    {
        $this->table = 'detailed_log';
        parent::__construct($level, $bubble);
    }

    protected function write(array $record): void
    {
        try {
            $request_id = request()->headers->get('X-Request-ID') ?? null;

            $data = array(
                'request_id' => $request_id,
                'message' => $record['message'],
                'context' => json_encode($record['context']),
                'level' => $record['level'],
                'level_name' => $record['level_name'],
                'channel' => $record['channel'],
                'record_datetime' => $record['datetime']->format('Y-m-d H:i:s'),
                'extra' => json_encode($record['extra']),
                'formatted' => $record['formatted'],
                'remote_addr' => $_SERVER['REMOTE_ADDR'],
                'created_at' => date("Y-m-d H:i:s"),
            );
            DB::connection()->table($this->table)->insert($data);
        } catch (\Exception $e){
            // silently ignore
        }
    }
}
