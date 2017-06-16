<?php

namespace Financial;


use Doctrine\Common\Collections\ArrayCollection;

class PaymentList
{
    /** @var ArrayCollection */
    private $list;
    /** @var ArrayCollection */
    private $currencies;

    public function __construct()
    {
        $this->list = new ArrayCollection;
        $this->currencies = new ArrayCollection;
    }

    public function addPayment(Payment $payment) :PaymentList
    {
        $this->list->add($payment);

        $currency = $payment->getValue()->getCurrency()->getCode();
        if(!$this->currencies->contains($currency)) {
            $this->currencies->add($currency);
        }
        return $this;
    }

    public function getList(): ArrayCollection
    {
        return $this->list;
    }

    public function getCurrencies(): ArrayCollection
    {
        return $this->currencies;
    }
}
