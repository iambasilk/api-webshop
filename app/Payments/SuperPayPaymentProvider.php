<?php
namespace App\Payments;
use Illuminate\Support\Facades\Http;

class SuperPayPaymentProvider implements PaymentProvider {

    private $paymentUrl = "https://superpay.view.agentur-loop.com/pay"; //TODO:add url to config

    public function capturePayment(string $orderId, float $amount, string $customerEmail): bool{
       
        $paymentRequest = [
            'order_id' => $orderId,
            'customer_email' => $customerEmail,
            'value' => $amount
        ];

        $response =  Http::withOptions(['verify' => false])
                        ->post($this->paymentUrl, $paymentRequest);

        if ($response->ok() && $response['message'] === 'Payment Successful') {
            return true;
        }

        return false; //TODO: Implement logging and retry mechanism for failed payments
    }

}