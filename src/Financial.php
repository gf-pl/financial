<?php
declare(strict_types = 1);

namespace Financial;

use Financial\Tools\xnpv;

final class Financial
{
    const MAX_ITERATIONS = 100;
    const ACCURACY = 1.0e-6;

    /**
     * @param SingleCurrencyPaymentList $paymentList
     * @param float $guess
     *
     * @return float|null
     */
    public function XIRR(SingleCurrencyPaymentList $paymentList, $guess = 0.1)
    {
        $x1 = 0.0;
        $x2 = $guess;
        $f1 = xnpv::calculate($x1, $paymentList);
        $f2 = xnpv::calculate($x2, $paymentList);
        for ($i = 0; $i < self::MAX_ITERATIONS; $i++)
        {
            if (($f1 * $f2) < 0.0) {
                break;
            }
            if (abs($f1) < abs($f2)) {
                $f1 = xnpv::calculate($x1 += 1.6 * ($x1 - $x2), $paymentList);
            } else {
                $f2 = xnpv::calculate($x2 += 1.6 * ($x2 - $x1), $paymentList);
            }
        }
        if (($f1 * $f2) > 0.0) {
            return null;
        }

        $f = xnpv::calculate($x1, $paymentList);
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
            $f_mid = xnpv::calculate($x_mid, $paymentList);
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
