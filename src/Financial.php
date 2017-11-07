<?php
declare(strict_types=1);

namespace Financial;

use Financial\Tools\npv;
use Financial\Tools\xnpv;
use Money\Money;

final class Financial
{
    const MAX_ITERATIONS = 100;
    const ACCURACY = 1.0e-6;

    /**
     * XIRR
     * Returns the internal rate of return for a schedule of cash flows
     * that is not necessarily periodic. To calculate the internal rate
     * of return for a series of periodic cash flows, use the IRR function.
     *
     * Adapted from routine in Numerical Recipes in C, and translated
     * from the Bernt A Oedegaard algorithm in C
     *
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
        for ($i = 0; $i < self::MAX_ITERATIONS; ++$i) {
            if (($f1 * $f2) < 0.0) {
                break;
            }
            if (\abs($f1) < \abs($f2)) {
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

        for ($i = 0; $i < self::MAX_ITERATIONS; ++$i) {
            $dx *= 0.5;
            $x_mid = $rtb + $dx;
            $f_mid = xnpv::calculate($x_mid, $paymentList);
            if ($f_mid <= 0.0) {
                $rtb = $x_mid;
            }
            if ((\abs($f_mid) < self::ACCURACY) || (\abs($dx) < self::ACCURACY)) {
                return $x_mid;
            }
        }
    }

    /**
     * IRR
     * Returns the internal rate of return for a series of cash flows
     * represented by the numbers in values. These cash flows do not
     * have to be even, as they would be for an annuity. However, the
     * cash flows must occur at regular intervals, such as monthly or
     * annually. The internal rate of return is the interest rate
     * received for an investment consisting of payments (negative
     * values) and income (positive values) that occur at regular periods.
     *
     * @param SingleCurrencyPaymentList $paymentList
     * @param float $guess
     *
     * @return float|null
     *
     * @internal param $values
     */
    public function IRR(SingleCurrencyPaymentList $paymentList, $guess = 0.1)
    {
        $x1 = 0.0;
        $x2 = $guess;
        $f1 = npv::calculate($x1, $paymentList);
        $f2 = npv::calculate($x2, $paymentList);
        for ($i = 0; $i < self::MAX_ITERATIONS; ++$i) {
            if (($f1 * $f2) < 0.0) {
                break;
            }
            if (\abs($f1) < \abs($f2)) {
                $f1 = npv::calculate($x1 += 1.6 * ($x1 - $x2), $paymentList);
            } else {
                $f2 = npv::calculate($x2 += 1.6 * ($x2 - $x1), $paymentList);
            }
        }
        if (($f1 * $f2) > 0.0) {
            return null;
        }

        $f = npv::calculate($x1, $paymentList);
        if ($f < 0.0) {
            $rtb = $x1;
            $dx = $x2 - $x1;
        } else {
            $rtb = $x2;
            $dx = $x1 - $x2;
        }

        for ($i = 0; $i < self::MAX_ITERATIONS; ++$i) {
            $dx *= 0.5;
            $x_mid = $rtb + $dx;
            $f_mid = npv::calculate($x_mid, $paymentList);
            if ($f_mid <= 0.0) {
                $rtb = $x_mid;
            }
            if ((\abs($f_mid) < self::ACCURACY) || (\abs($dx) < self::ACCURACY)) {
                return $x_mid;
            }
        }
    }

    /**
     * SYD
     * Returns the sum-of-years' digits depreciation of an asset for
     * a specified period.
     * https://support.office.com/pl-pl/article/SYD-funkcja-069f8106-b60b-4ca2-98e0-2a0f206bdb27.
     *
     * @param Money $cost is the initial cost of the asset
     * @param Money $salvage is the value at the end of the depreciation (sometimes called the salvage value of the asset)
     * @param int $life is the number of periods over which the asset is depreciated (sometimes called the useful life of the asset)
     * @param int $period is the period and must use the same units as life
     *
     * @throws \InvalidArgumentException
     *
     * @return Money|null
     */
    public function SYD(Money $cost, Money $salvage, int $life, int $period)
    {
        if ($cost->getCurrency()->getCode() !== $salvage->getCurrency()->getCode()) {
            throw new \InvalidArgumentException('');
        }
        $sydValue = (($cost->getAmount() - $salvage->getAmount()) * ($life - $period + 1) * 2) / ($life * (1 + $life));
        $sydValue = \round($sydValue);

        return \is_finite($sydValue) ? new Money($sydValue, $cost->getCurrency()) : null;
    }

    /**
     * DDB
     * Returns the depreciation of an asset for a specified period using
     * the double-declining balance method or some other method you specify.
     * https://support.office.com/pl-pl/article/DDB-funkcja-519a7a37-8772-4c96-85c0-ed2c209717a5
     *
     * @param Money $cost is the initial cost of the asset.
     * @param Money $salvage is the value at the end of the depreciation (sometimes called the salvage value of the asset).
     * @param integer $life is the number of periods over which the asset is being depreciated (sometimes called the useful life of the asset).
     * @param integer $period is the period for which you want to calculate the depreciation. Period must use the same units as life.
     * @param float $factor is the rate at which the balance declines. If factor is omitted, it is assumed to be 2 (the double-declining balance method).
     *
     * @return Money
     * @throws \InvalidArgumentException
     */
    public function DDB(Money $cost, Money $salvage, int $life, int $period, $factor = 2.0)
    {
        if ($cost->getCurrency()->getCode() !== $salvage->getCurrency()->getCode()) {
            throw new \InvalidArgumentException('');
        }

        $x = 0;
        $n = 0;
        $costValue = $cost->getAmount();
        while ($period > $n) {
            $x = $factor * $costValue / $life;
            if (($costValue - $x) < $salvage->getAmount()) {
                $x = $costValue - $salvage->getAmount();
            }
            if ($x < 0) {
                $x = 0;
            }
            $costValue -= $x;
            $n++;
        }
        return \is_finite($x) ? new Money(round($x), $cost->getCurrency()) : null;
    }
}
