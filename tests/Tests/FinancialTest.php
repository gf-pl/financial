<?php

namespace Tests\Financial;


use Financial\Financial;
use Financial\Payment;
use Financial\PaymentList;
use Money\Currency;
use Money\Money;
use PHPUnit\Framework\TestCase;

final class FinancialTest extends TestCase
{
    public function test_xirr()
    {

        $paymentList = new PaymentList();
        $paymentList->addPayment(new Payment(Money::EUR(100), new \DateTime('2016-01-01')));
        $paymentList->addPayment(new Payment(Money::EUR(-120), new \DateTime('2016-12-31')));
        $paymentList = $paymentList->recalculateToGivenCurrency(new Currency('EUR'));

        $financial = new Financial();
        $this->assertFinite($financial->XIRR($paymentList));
        $this->assertGreaterThanOrEqual(abs($financial->XIRR($paymentList) - 0.2), 0.002);

    }

}
