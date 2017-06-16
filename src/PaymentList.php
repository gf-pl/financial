<?php

namespace Financial;


use Doctrine\Common\Collections\ArrayCollection;

class PaymentList
{
    /** @var ArrayCollection */
    private $list;

    public function __construct()
    {
        $this->list = new ArrayCollection;
    }

    public function addPayment(Payment $payment) :PaymentList
    {
        $this->list->add($payment);

        return $this;
    }

    public function getList(): ArrayCollection
    {
        return $this->list;
    }

}
