<?php

namespace Financial;


use Doctrine\Common\Collections\ArrayCollection;
use Money\Converter;
use Money\Currencies\ISOCurrencies;
use Money\Currency;
use Money\Exchange\SwapExchange;
use Swap\Builder;

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

    public function recalculateToGivenCurrency(Currency $currency) :SingleCurrencyPaymentList
    {
        $swap = (new Builder())
            ->add('fixer')
            ->add('yahoo')
            ->build();
        $exchange = new SwapExchange($swap);
        $converter = new Converter(new ISOCurrencies(), $exchange);

        $listWithOneCurrency = new SingleCurrencyPaymentList();

        foreach ($this->getList()->getValues() as &$value) {
            /** @var Payment $value */
            $newValue = $converter->convert($value->getValue(), $currency);
            $date = $value->getDate();
            $listWithOneCurrency->addPayment(new Payment($newValue, $date));
        }

        return $listWithOneCurrency;

    }
}
