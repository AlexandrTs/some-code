<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class APIRequestLog extends Model
{
    use HasFactory;

    protected $table = 'api_request_logs';

    public  $timestamps = false;

}
