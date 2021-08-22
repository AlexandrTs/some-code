<?php
declare(strict_types=1);

namespace App\Contracts;

interface APIServiceLoggerInterface {

    public function __construct(APILoggerInterface $logger);

}
