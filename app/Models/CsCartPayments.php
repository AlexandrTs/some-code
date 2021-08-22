<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CsCartPayments extends Model
{
    use HasFactory;

    protected $connection = 'cscart';

    protected $table = 'cscart_payments';

    public static function processorInfoByPaymentId($payment_id)
    {
        $data = CsCartPayments::where('cscart_payments.payment_id', $payment_id)
            ->value('cscart_payments.processor_params');

        return !empty($data) ? unserialize($data) : null;
    }

}
