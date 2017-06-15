<?php

namespace Tests\Financial;


use Financial\Payment;
use Financial\PaymentList;
use Money\Money;
use PHPUnit\Framework\TestCase;

final class PaymentListTest extends TestCase
{
    function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
    }

    function test_payment_list_values_count()
    {

        $paymentList = new PaymentList();

        $this->assertEquals($paymentList->getList()->count(), 0);

        $paymentList->addPayment(new Payment(Money::EUR(200), new \DateTime()));
        $this->assertEquals($paymentList->getList()->count(), 1);

        $paymentList->addPayment(new Payment(Money::PLN(300), new \DateTime()));
        $this->assertEquals($paymentList->getList()->count(), 2);
    }

}
