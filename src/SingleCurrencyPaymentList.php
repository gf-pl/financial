<?php
declare(strict_types=1);

namespace Financial;

class SingleCurrencyPaymentList extends PaymentList
{
    public function addPayment(Payment $payment): PaymentList
    {
        if ($this->getCurrencies()->count() > 0 && $payment->getValue()->getCurrency()->getCode() !== $this->getCurrencies()->first()) {
            throw new \InvalidArgumentException('This list can contain only payments in ' . $this->getCurrencies()->first() . 'currency');
        }

        return parent::addPayment($payment);
    }
}
