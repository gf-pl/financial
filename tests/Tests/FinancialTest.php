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

    public function test_irr()
    {
        $paymentList = new PaymentList();
        $paymentList->addPayment(new Payment(Money::EUR(100), new \DateTime('2016-01-01')));
        $paymentList->addPayment(new Payment(Money::EUR(-100), new \DateTime('2016-12-31')));
        $paymentList = $paymentList->recalculateToGivenCurrency(new Currency('EUR'));

        $financial = new Financial();
        $this->assertFinite($financial->IRR($paymentList));
        $this->assertEquals(0, round($financial->XIRR($paymentList), 4));
    }

    public function test_syd()
    {
        $financial = new Financial();
        $this->assertEquals(Money::EUR(4090.91 * 100), $financial->SYD(Money::EUR(30000 * 100), Money::EUR(7500 * 100), 10, 1), 2);
        $this->assertEquals(Money::EUR(409.09 * 100), $financial->SYD(Money::EUR(30000 * 100), Money::EUR(7500 * 100), 10, 10), 2);
    }
}
