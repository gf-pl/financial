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
        $this->assertEquals(0.2, round($financial->XIRR($paymentList), 4));
    }

    public function test_xirr_if_investition_doubled()
    {
        $paymentList = new PaymentList();
        $paymentList->addPayment(new Payment(Money::EUR(100), new \DateTime('2016-01-01')));
        $paymentList->addPayment(new Payment(Money::EUR(-200), new \DateTime('2016-12-31')));
        $paymentList = $paymentList->recalculateToGivenCurrency(new Currency('EUR'));

        $financial = new Financial();
        $this->assertFinite($financial->XIRR($paymentList));
        $this->assertEquals(1, round($financial->XIRR($paymentList), 4));
    }

    public function test_xirr_if_investition_zeroed()
    {
        $paymentList = new PaymentList();
        $paymentList->addPayment(new Payment(Money::EUR(100), new \DateTime('2016-01-01')));
        $paymentList->addPayment(new Payment(Money::EUR(100), new \DateTime('2016-07-01')));
        $paymentList->addPayment(new Payment(Money::EUR(-200), new \DateTime('2016-12-31')));
        $paymentList = $paymentList->recalculateToGivenCurrency(new Currency('EUR'));

        $financial = new Financial();
        $this->assertFinite($financial->XIRR($paymentList));
        $this->assertEquals(0, round($financial->XIRR($paymentList), 4));
    }
}
