<?php
declare(strict_types = 1);

namespace Financial;


use Money\Money;

class Payment
{
    /** @var Money */
    private $value;
    /** @var \DateTime */
    private $date;

    public function __construct(Money $value, \DateTime $date)
    {
        $this->value = $value;
        $this->date = $date;
    }

    public function getValue(): Money
    {
        return $this->value;
    }

    public function getDate(): \DateTime
    {
        return $this->date;
    }
}
