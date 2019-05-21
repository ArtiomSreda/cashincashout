<?php

use PHPUnit\Framework\TestCase;

final class WeekCheckerHelperTest extends TestCase
{
    public function testOrStringDatesIsInSameWeek(): void
    {
        $this->assertTrue(\Cashincashout\Helpers\WeekCheckerHelper::checkWeekInterval('2018-12-31', '2019-01-01'));
    }

    public function testOrStringDatesIsNotInSameWeek(): void
    {
        $this->assertFalse(\Cashincashout\Helpers\WeekCheckerHelper::checkWeekInterval('2017-12-31', '2019-01-01'));
    }


    public function testOrWorkingWrongDatesException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        \Cashincashout\Helpers\WeekCheckerHelper::checkWeekInterval('invalid_date_1', '123456789');
    }

}