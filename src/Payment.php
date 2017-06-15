<?php

namespace Financial;


use Money\Money;

class Payment
{
    private $value;
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
