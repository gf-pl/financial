<?php

namespace Financial;


final class DateDiff
{
    /** @var \DateTime */
    private $startDate;
    /** @var \DateTime */
    private $endDate;

    public function __construct(\DateTime $startDate, \DateTime $endDate)
    {
        $this->startDate = $startDate->setTime(0, 0, 0);
        $this->endDate = $endDate->setTime(0, 0, 0);
    }

    public function days(): int
    {
        return ceil($this->endDate->diff($this->startDate)->days);
    }

    public function weeks(): int
    {
        return ceil(($this->endDate->diff($this->startDate)->days) / 7);
    }

    public function months(): int
    {
        return ((int)$this->endDate->format('Y') - (int)$this->startDate->format('Y')) * 12
            + ((int)$this->endDate->format('m') - (int)$this->startDate->format('m'));
    }

    public function years(): int
    {
        return $this->endDate->diff($this->startDate)->y;
    }
}

