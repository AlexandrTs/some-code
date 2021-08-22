<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EcomOrderStatusRequest extends Model
{
    use HasFactory;

    protected $connection = 'jysan_payment';

    protected $table = 'jpayment_ecom_order_status_request';

    public  $timestamps = true;

}
