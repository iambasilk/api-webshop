<?php
namespace App\Payments;
use App\Payments\SuperPayPaymentProvider;
use Exception;

class PaymentProviderFactory
{
    public static function createPaymentProvider(string $providerName): PaymentProvider
    {
        switch ($providerName) {
            case 'SUPER_PAY':
                return new SuperPayPaymentProvider();
            // add future providers here
            default:
                throw new Exception('Payment provider not found: ' . $providerName);
        }
    }
}
