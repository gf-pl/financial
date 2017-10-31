<?php
declare(strict_types = 1);

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
        $conversionNeeded = true;
        if ($this->getCurrencies()->count() === 1 && $currency->getCode() === $this->getCurrencies()->first())
        {
            $conversionNeeded = false;
        }

        $swap = (new Builder())
            ->add('fixer')
            ->add('yahoo')
            ->build();
        $exchange = new SwapExchange($swap);
        $converter = new Converter(new ISOCurrencies(), $exchange);

        $listWithOneCurrency = new SingleCurrencyPaymentList();

        foreach ($this->getList()->getValues() as &$value) {
            /** @var Payment $value */
            $newValue = $value->getValue();
            if(true === $conversionNeeded) {
                $newValue = $converter->convert($value->getValue(), $currency);
            }
            $listWithOneCurrency->addPayment(new Payment($newValue, $value->getDate()));
        }

        return $listWithOneCurrency;

    }
}
