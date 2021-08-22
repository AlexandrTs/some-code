<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CsCartLog extends Model
{
    use HasFactory;

    protected $connection = 'cscart';

    protected $table = 'cscart_logs';

    public  $timestamps = false;
}
