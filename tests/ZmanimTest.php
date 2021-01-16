<?php

use Carbon\Carbon;
use Kosherdev\Models\Location;
use Kosherdev\Zmanim\Zmanim;
use PHPUnit\Framework\TestCase;

class ZmanimTest extends TestCase
{
    public function testStartTishaBeAv()
    {
        $location = new Location(59.434350, 24.739664, 0);
        $zmanim = new Zmanim($location);
        $zmanim->setCurrentDate(Carbon::parse('30-07-2020'));
        var_dump($zmanim->getFastTimes());
        $this->assertTrue(count($zmanim->getFastTimes()) > 0);
    }
}
