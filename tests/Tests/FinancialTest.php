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
        $this->assertEquals(Money::EUR(4090.91 * 100), $financial->SYD(Money::EUR(30000 * 100), Money::EUR(7500 * 100), 10, 1));
        $this->assertEquals(Money::EUR(409.09 * 100), $financial->SYD(Money::EUR(30000 * 100), Money::EUR(7500 * 100), 10, 10));

        $this->expectException(\InvalidArgumentException::class);
        $this->assertEquals(Money::EUR(409.09 * 100), $financial->SYD(Money::USD(30000 * 100), Money::EUR(7500 * 100), 10, 10));
    }

    public function test_ddb()
    {
        $financial = new Financial();
        $this->assertEquals(Money::EUR(1.32 * 100), $financial->DDB(Money::EUR(2400 * 100), Money::EUR(300 * 100), 10 * 365, 1));
        $this->assertEquals(Money::EUR(40 * 100), $financial->DDB(Money::EUR(2400 * 100), Money::EUR(300 * 100), 10 * 12, 1));
        $this->assertEquals(Money::EUR(480 * 100), $financial->DDB(Money::EUR(2400 * 100), Money::EUR(300 * 100), 10, 1));
        $this->assertEquals(Money::EUR(306 * 100), $financial->DDB(Money::EUR(2400 * 100), Money::EUR(300 * 100), 10, 2, 1.5));
        $this->assertEquals(Money::EUR(22.12 * 100), $financial->DDB(Money::EUR(2400 * 100), Money::EUR(300 * 100), 10, 10));

        $this->expectException(\InvalidArgumentException::class);
        $this->assertEquals(Money::EUR(22.12 * 100), $financial->DDB(Money::PLN(2400 * 100), Money::EUR(300 * 100), 10, 10));
    }
}
