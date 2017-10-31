<?php
declare(strict_types=1);

namespace Financial\Tools;

use Financial\Payment;
use Financial\SingleCurrencyPaymentList;

final class xnpv
{
    /**
     * @param $rate
     * @param SingleCurrencyPaymentList $paymentList
     *
     * @return float|null
     */
    public static function calculate($rate, SingleCurrencyPaymentList $paymentList)
    {
        /** @var float $xnpv */
        $xnpv = 0.0;

        foreach ($paymentList->getList() as $payment) {
            /* @var Payment $payment */
            $xnpv += $payment->getValue()->getAmount() / ((1 + $rate) ** ((new DateDiff($paymentList->getList()->first()->getDate(), $payment->getDate()))->days() / 365));
        }

        return \is_finite($xnpv) ? $xnpv : null;
    }
}
