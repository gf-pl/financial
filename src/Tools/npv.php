<?php
declare(strict_types = 1);

namespace Financial\Tools;

use Financial\Payment;
use Financial\SingleCurrencyPaymentList;

final class npv
{
    /**
     * @param $rate
     * @param SingleCurrencyPaymentList $paymentList
     *
     * @return float|null
     */
    public static function calculate($rate, SingleCurrencyPaymentList $paymentList)
    {
        /** @var float $npv */
        $npv = 0.0;
        $i = 0;

        foreach ($paymentList->getList() as $payment) {
            /** @var Payment $payment */
            $npv += $payment->getValue()->getAmount() / ((1 + $rate) ** ($i + 1));
        }

        return (is_finite($npv) ? $npv: null);
    }
}
