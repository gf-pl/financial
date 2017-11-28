<?php
declare(strict_types=1);

namespace Financial;

use Assert\Assert;
use Assert\Assertion;
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
     * https://support.office.com/pl-pl/article/XIRR-funkcja-de1242ec-6477-445b-b11b-a303ad9adc9d
     *
     * @param SingleCurrencyPaymentList $paymentList
     * @param float $guess
     *
     * @return float|null
     */
    public function XIRR(SingleCurrencyPaymentList $paymentList, float $guess = 0.1)
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

        return null;
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
     * https://support.office.com/pl-pl/article/IRR-funkcja-64925eaa-9988-495b-b290-3ad0c163c1bc
     *
     * @param SingleCurrencyPaymentList $paymentList
     * @param float $guess
     *
     * @return float|null
     *
     * @internal param $values
     */
    public function IRR(SingleCurrencyPaymentList $paymentList, float $guess = 0.1)
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

        return null;
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
        $this->verifyCurrencyEquality($cost, $salvage);

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
     * @return Money|null
     * @throws \InvalidArgumentException
     */
    public function DDB(Money $cost, Money $salvage, int $life, int $period, $factor = 2.0)
    {
        $this->verifyCurrencyEquality($cost, $salvage);

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

    /**
     * SLN
     * Returns the straight-line depreciation of an asset for one period.
     * https://support.office.com/pl-pl/article/SLN-funkcja-cdb666e5-c1c6-40a7-806a-e695edc2f1c8
     *
     * @param  Money $cost is the initial cost of the asset.
     * @param  Money $salvage is the value at the end of the depreciation (sometimes called the salvage value of the asset).
     * @param  integer $life is the number of periods over which the asset is being depreciated (sometimes called the useful life of the asset).
     *
     * @return Money|null
     * @throws \InvalidArgumentException
     */
    public function SLN(Money $cost, Money $salvage, int $life)
    {
        $this->verifyCurrencyEquality($cost, $salvage);

        $sln = ($cost->getAmount() - $salvage->getAmount()) / $life;

        return (is_finite($sln) ? new Money(round($sln), $cost->getCurrency()) : null);
    }

    /**
     * VDB
     * Returns the depreciation of an asset for any period you specify,
     * including partial periods, using the double-declining balance method
     * or some other method you specify. VDB stands for variable declining balance.
     * https://support.office.com/pl-pl/article/VDB-funkcja-dde4e207-f3fa-488d-91d2-66d55e861d73
     *
     * @param  Money   $cost         is the initial cost of the asset.
     * @param  Money   $salvage      is the value at the end of the depreciation (sometimes called the salvage value of the asset).
     * @param  integer $life         is the number of periods over which the asset is depreciated (sometimes called the useful life of the asset).
     * @param  integer $start_period is the starting period for which you want to calculate the depreciation. Start_period must use the same units as life.
     * @param  integer $end_period   is the ending period for which you want to calculate the depreciation. End_period must use the same units as life.
     * @param  float   $factor       is the rate at which the balance declines. If factor is omitted, it is assumed to be 2 (the double-declining balance method). Change factor if you do not want to use the double-declining balance method.
     * @param  bool    $no_switch    is a logical value specifying whether to switch to straight-line depreciation when depreciation is greater than the declining balance calculation.
     * @return Money   the depreciation of an asset.
     */
    public function VDB(Money $cost, Money $salvage, int $life, int $start_period, int $end_period, float $factor = 2.0, bool $no_switch = false)
    {
        // pre-validations
        if ($start_period < 0
            || $factor <= 0
            || $end_period < $start_period
            || $end_period > $life
            || $cost->getAmount() < 0
            || $salvage->getAmount() > $cost->getAmount())
        {
            return null;
        }

        $fVdb = 0.0;
        $fIntStart = floor($start_period);
        $fIntEnd = ceil($end_period);

        if ($no_switch) {
            $nLoopStart = (int) $fIntStart;
            $nLoopEnd = (int) $fIntEnd;

            for ($i = $nLoopStart + 1; $i <= $nLoopEnd; $i++) {
                $fTerm = $this->_ScGetGDA((float) $cost->getAmount(), (float) $salvage->getAmount(), $life, $i, $factor);
                if ($i === $nLoopStart + 1) {
                    $fTerm *= (min($end_period, $fIntStart + 1.0) - $start_period);
                }
                elseif ($i === $nLoopEnd) {
                    $fTerm *= ($end_period + 1.0 - $fIntEnd);
                }
                $fVdb += $fTerm;
            }
        } else {
            $life1 = $life;

            if ($start_period !== $fIntStart) {
                if ($factor > 1) {
                    if ($start_period >= ($life / 2)) {
                        $fPart = $start_period - ($life / 2);
                        $start_period = $life / 2;
                        $end_period -= $fPart;
                        ++$life1;
                    }
                }
            }

            $cost->subtract($this->_ScInterVDB($cost, $salvage, $life, $life1, $start_period, $factor));
            $fVdb = $this->_ScInterVDB($cost, $salvage, $life, $life - $start_period, $end_period - $start_period, $factor);
        }

        return $fVdb;
    }

    private function verifyCurrencyEquality(Money $var1, Money $var2)
    {
        Assertion::eq($var1->getCurrency(), $var2->getCurrency());
    }

    private function _ScGetGDA(float $fWert, float $fRest, float $fDauer, float $fPeriode, float $fFaktor): float
    {
        $fZins = $fFaktor / $fDauer;
        if ($fZins >= 1.0) {
            $fZins = 1.0;
            $fAlterWert = 0.0;
            if ($fPeriode === 1.0) {
                $fAlterWert = $fWert;
            }
        } else {
            $fAlterWert = $fWert * ((1.0 - $fZins) ** ($fPeriode - 1.0));
        }

        $fNeuerWert = $fWert * ((1.0 - $fZins) ** $fPeriode);

        if ($fNeuerWert < $fRest) {
            $fGda = $fAlterWert - $fRest;
        }
        else {
            $fGda = $fAlterWert - $fNeuerWert;
        }

        if ($fGda < 0.0) {
            $fGda = 0.0;
        }

        return $fGda;
    }

    private function _ScInterVDB(Money $cost, Money $salvage, float $life, float $life1, float $period, float $factor): Money
    {
        $this->verifyCurrencyEquality($cost, $salvage);

        $fVdb       = 0;
        $fIntEnd    = ceil($period);
        $nLoopEnd   = $fIntEnd;
        $fRestwert  = $cost->getAmount() - $salvage->getAmount();
        $bNowLia    = false;

        $fLia = 0;
        for ($i = 1; $i <= $nLoopEnd; $i++) {
            if (!$bNowLia) {
                $fGda = $this->_ScGetGDA(
                    (float) $cost->getAmount(),
                    (float) $salvage->getAmount(),
                    $life,
                    $i,
                    $factor
                );
                $fLia = $fRestwert / ($life1 - (float)($i - 1));

                if ($fLia > $fGda) {
                    $fTerm   = $fLia;
                    $bNowLia = true;
                } else {
                    $fTerm      = $fGda;
                    $fRestwert -= $fGda;
                }
            } else {
                $fTerm = $fLia;
            }

            if ($i === $nLoopEnd) {
                $fTerm *= ($period + 1.0 - $fIntEnd);
            }
            $fVdb += $fTerm;
        }
        return new Money(round($fVdb), $cost->getCurrency());
    }
}
