<?php

namespace Tests\Financial;


use Financial\Payment;
use Financial\PaymentList;
use Money\Currency;
use Money\Money;
use PHPUnit\Framework\TestCase;

final class PaymentListTest extends TestCase
{
    public function test_payment_list_values_count()
    {

        $paymentList = new PaymentList();

        $this->assertEquals($paymentList->getList()->count(), 0);

        $paymentList->addPayment(new Payment(Money::EUR(200), new \DateTime()));
        $this->assertEquals($paymentList->getList()->count(), 1);
        $this->assertEquals($paymentList->getCurrencies()->count(), 1);

        $paymentList->addPayment(new Payment(Money::PLN(300), new \DateTime()));
        $this->assertEquals($paymentList->getList()->count(), 2);
        $this->assertEquals($paymentList->getCurrencies()->count(), 2);

        $paymentList->addPayment(new Payment(Money::EUR(427), new \DateTime()));
        $this->assertEquals($paymentList->getList()->count(), 3);
        $this->assertEquals($paymentList->getCurrencies()->count(), 2);

        $listInOneCurrency = $paymentList->recalculateToGivenCurrency(new Currency('USD'));
        $this->assertEquals($listInOneCurrency->getList()->count(), 3);
        $this->assertEquals($listInOneCurrency->getCurrencies()->count(), 1);
        $this->assertEquals($listInOneCurrency->getCurrencies()->first(), 'USD');
    }

}
