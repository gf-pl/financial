<?php

namespace Tests\Financial;

use Financial\Payment;
use Financial\PaymentList;
use Financial\SingleCurrencyPaymentList;
use Money\Currency;
use Money\Money;
use PHPUnit\Framework\TestCase;

final class SingleCurrencyPaymentListTest extends TestCase
{
    public function test_exception_when_second_currency_added()
    {
        $paymentList = new SingleCurrencyPaymentList();

        $this->assertEquals($paymentList->getList()->count(), 0);

        $paymentList->addPayment(new Payment(Money::EUR(200), new \DateTime()));
        $this->assertEquals($paymentList->getList()->count(), 1);
        $this->assertEquals($paymentList->getCurrencies()->count(), 1);

        $paymentList->addPayment(new Payment(Money::EUR(482), new \DateTime()));

        $this->expectException(\InvalidArgumentException::class);
        $paymentList->addPayment(new Payment(Money::USD(200), new \DateTime()));

    }

}
