<?php
namespace App\Payments;

class PaymentProcessor
{
    private PaymentProvider $paymentProvider;

    public function __construct(PaymentProvider $paymentProvider)
    {
        $this->paymentProvider = $paymentProvider;
    }

    public function processPayment(string $orderId, float $amount, string $customerEmail, ): bool
    {
        return $this->paymentProvider->capturePayment($orderId, $amount, $customerEmail);
    }
}