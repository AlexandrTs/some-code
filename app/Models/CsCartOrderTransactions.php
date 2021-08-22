<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CsCartOrderTransactions extends Model
{
    use HasFactory;

    protected $connection = 'cscart';

    protected $table = 'cscart_jysan_crystal_order_transactions';

}
