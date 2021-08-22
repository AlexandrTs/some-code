<?php
declare(strict_types=1);

namespace App\Contracts;

interface APILoggerInterface {

    public static function log(array $array, string $executor): bool;

}
