<?php
namespace App\Payments;

interface PaymentProvider
{
    public function capturePayment(string $orderId, float $amount, string $customerEmail): bool;
}
