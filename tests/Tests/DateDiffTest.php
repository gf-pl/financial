<?php

namespace Tests\Financial;


use Financial\DateDiff;
use PHPUnit\Framework\TestCase;

final class DateDiffTest extends TestCase
{
    private $endDate;
    private $startDate;

    function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->startDate = new \DateTime('2016-01-01');
        $this->endDate = new \DateTime('2017-02-10');
    }

    function test_date_diff_differences()
    {

        $dateDiff = new DateDiff($this->startDate, $this->endDate);

        $this->assertEquals($dateDiff->years(), 1);
        $this->assertEquals($dateDiff->days(), 40+366);
        $this->assertEquals($dateDiff->weeks(), 58);
        $this->assertEquals($dateDiff->months(), 13);
    }

}
