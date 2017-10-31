<?php

namespace Financial;


final class Financial
{
    const MAX_ITERATIONS = 100;
    const ACCURACY = 1.0e-6;

    public function XIRR(SingleCurrencyPaymentList $paymentList, $guess = 0.1)
    {
        // create an initial bracket, with a root somewhere between bot and top
        $x1 = 0.0;
        $x2 = $guess;
        $f1 = Tools::XNPV($x1, $paymentList);
        $f2 = Tools::XNPV($x2, $paymentList);
        for ($i = 0; $i < self::MAX_ITERATIONS; $i++)
        {
            if (($f1 * $f2) < 0.0) {
                break;
            }
            if (abs($f1) < abs($f2)) {
                $f1 = Tools::XNPV($x1 += 1.6 * ($x1 - $x2), $paymentList);
            } else {
                $f2 = Tools::XNPV($x2 += 1.6 * ($x2 - $x1), $paymentList);
            }
        }
        if (($f1 * $f2) > 0.0) {
            return null;
        }

        $f = Tools::XNPV($x1, $paymentList);
        if ($f < 0.0) {
            $rtb = $x1;
            $dx = $x2 - $x1;
        } else {
            $rtb = $x2;
            $dx = $x1 - $x2;
        }

        for ($i = 0; $i < self::MAX_ITERATIONS; $i++)
        {
            $dx *= 0.5;
            $x_mid = $rtb + $dx;
            $f_mid = Tools::XNPV($x_mid, $paymentList);
            if ($f_mid <= 0.0) {
                $rtb = $x_mid;
            }
            if ((abs($f_mid) < self::ACCURACY) || (abs($dx) < self::ACCURACY)) {
                return $x_mid;
            }
        }
        return null;
    }
}
