<?php


use Carbon\Carbon;
use Kosherdev\Calendar\JewishCalendar;
use PHPUnit\Framework\TestCase;

class ToraParshiyosTest extends TestCase
{
    public function testGetParsha()
    {
        $now = Carbon::now()->setYear(2021)->setMonth(4)->setDay(10);
        $parsha = JewishCalendar::getParsha($now);
        var_dump($parsha);
        $this->assertTrue($parsha == 'Shmini');
    }
}