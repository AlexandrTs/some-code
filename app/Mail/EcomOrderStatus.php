<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EcomOrderStatus extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Array with API request/response data
     *
     * @var array
     */
    public $data;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = [];
        foreach($data as $row) {
            $this->data[] = [
                'header' => 'Order: ' . $row['order_id'],
                'content' => str_replace(
                    "\n",
                    '<br>',
                    json_encode($row, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)
                )
            ];
        }
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->subject('Ecom order status (JysanPaymentSrv)')
            ->view('email.order_status');
    }
}
