<?php

namespace Kosherdev\Zmanim;


use Carbon\Carbon;
use Kosherdev\Models\Location;

class Zmanim
{
    private $hoursOffset = 0;
    private $currentDate;
    private $lat;
    private $lng;
    private const timeFormat = 'H:i';

    public function __construct(Location $location)
    {
        $this->lat = $location->getLat();
        $this->lng = $location->getLng();
        $this->hoursOffset = $location->getOffset();
        $this->currentDate = Carbon::now()->setTime(0, 0, 0);
        $this->hoursOffset = $location->getOffset() ?? 0;
    }

    /**
     * @return Carbon
     */
    public function getCurrentDate(): Carbon
    {
        return $this->currentDate;
    }

    /**
     * @param Carbon $current_date
     * @return Carbon
     */
    public function setCurrentDate(Carbon $current_date)
    {
        return $this->currentDate = $current_date;
    }

    /**
     * @return Carbon
     */
    public function getSunrise(): Carbon
    {
        return $this->_getProportionalSunrise(90 + 50 / 60);
    }

    /**
     * @return Carbon
     */
    public function getSunset(): Carbon
    {
        $sunset = $this->_getProportionalSunset(90 + 50 / 60);
//        if ($this->getSunrise()->gt($sunset)) {
//            $sunset = $this->_getProportionalSunset(90 + 50 / 60, $this->getCurrentDate()->addDay()->timestamp);
//        }

        return $sunset;
    }

    /**
     * @param $zenith
     * @return Carbon
     */
    private function _getProportionalSunrise($zenith): Carbon
    {
        $datetime = date_sunrise($this->getCurrentDate()->timestamp, SUNFUNCS_RET_TIMESTAMP, $this->lat, $this->lng, $zenith, $this->hoursOffset);
        return $this->getCurrentDate()->setTimestamp($datetime);
    }

    /**
     * @param $zenith
     * @param bool $timestamp
     * @return Carbon
     */
    private function _getProportionalSunset($zenith, $timestamp = false): Carbon
    {
        $datetime = date_sunset($timestamp ? $timestamp : $this->getCurrentDate()->timestamp, SUNFUNCS_RET_TIMESTAMP, $this->lat, $this->lng, $zenith, $this->hoursOffset);
        return $this->getCurrentDate()->setTimestamp($datetime);
    }

    /**
     * @param $proportionalHour
     * @return array
     */
    function _getProportionalHours($proportionalHour)
    {
        list($startOfDayHour, $startOfDayMin) = explode(':', $this->getSunrise()->format(self::timeFormat));
        list($endOfDayHour, $endOfDayMin) = explode(':', $this->getSunset()->format(self::timeFormat));
        $startOfDayInMinutesAfterMidnight = $startOfDayHour * 60 + $startOfDayMin;
        $endOfDayInMinutesAfterMidnight = $endOfDayHour * 60 + $endOfDayMin;
        $resultInMinutesAfterMidnight = (int)($startOfDayInMinutesAfterMidnight +
            (($endOfDayInMinutesAfterMidnight - $startOfDayInMinutesAfterMidnight) *
                $proportionalHour) / 12);
        $Min = $resultInMinutesAfterMidnight % 60;
        if (strlen($Min) < 2 && $Min == 0) $Min = $Min . '0';
        if (strlen($Min) < 2 && $Min > 0) $Min = '0' . $Min;
        return [(int)($resultInMinutesAfterMidnight / 60),
            $Min];
    }

    /**
     * @return string
     */
    public function getAlot()
    {
        return $this->_getProportionalSunrise(90 + 16.1)->format(self::timeFormat);
    }

    /**
     * @return string
     */
    public function getAlot72()
    {
        $time = $this->_getProportionalHours(-1.2);
        return $this->getCurrentDate()->setTime($time[0], $time[1])->format(self::timeFormat);
    }

    /**
     * @return string
     */
    public function getAlot90()
    {
        $time = $this->_getProportionalHours(6);
        return $this->getCurrentDate()->setTime($time[0], $time[1])->format(self::timeFormat);
    }

    /**
     * @return string
     */
    public function getTallit()
    {
        return $this->_getProportionalSunrise(90 + 11.5)->format(self::timeFormat);
    }

    /**
     * @return string
     */
    public function getShmaGRO()
    {
        $time = $this->_getProportionalHours(3);
        return $this->getCurrentDate()->setTime($time[0], $time[1])->format(self::timeFormat);
    }

    /**
     * @return string
     */
    public function getShmaMA()
    {
        $time = $this->_getProportionalHours(3);
        return $this->getCurrentDate()->setTime($time[0], $time[1])->subMinutes(36)->format(self::timeFormat);
    }

    /**
     * @return string
     */
    public function getNetz()
    {
        return $this->getSunrise()->format(self::timeFormat);
    }

    /**
     * @return string
     */
    public function getTfilaGRO()
    {
        $time = $this->_getProportionalHours(4);
        return $this->getCurrentDate()->setTime($time[0], $time[1])->format(self::timeFormat);
    }

    /**
     * @return string
     */
    public function getChatzot()
    {
        $time = $this->_getProportionalHours(6);
        return $this->getCurrentDate()->setTime($time[0], $time[1])->format(self::timeFormat);
    }

    /**
     * @return string
     */
    public function getMinchaGdolaGRO()
    {
        $time = $this->_getProportionalHours(6.5);
        return $this->getCurrentDate()->setTime($time[0], $time[1])->format(self::timeFormat);
    }

    /**
     * @return string
     */
    public function getMinchaGdolaMA()
    {
        $time = $this->_getProportionalHours(6.5);
        return $this->getCurrentDate()->setTime($time[0], $time[1])->addSeconds(45)->format(self::timeFormat);
    }

    /**
     * @return string
     */
    public function getMinchaKtanaGRO()
    {
        $time = $this->_getProportionalHours(9.5);
        return $this->getCurrentDate()->setTime($time[0], $time[1])->format(self::timeFormat);
    }

    /**
     * @return string
     */
    public function getMinchaKtanaMA()
    {
        $time = $this->_getProportionalHours(9.5);
        return $this->getCurrentDate()->setTime($time[0], $time[1])->addSeconds(57)->format(self::timeFormat);
    }

    /**
     * @return string
     */
    public function getPlaghaMincha()
    {
        $time = $this->_getProportionalHours(10.75);
        return $this->getCurrentDate()->setTime($time[0], $time[1])->format(self::timeFormat);
    }

    /**
     * @return string
     */
    public function getCandleLighting()
    {
        return $this->getSunset()->subMinutes(18)->format(self::timeFormat);
    }

    /**
     * @return string
     */
    public function getCandleLightingThisWeek()
    {
        return $this->_getProportionalSunset(90 + 50 / 60, $this->getCurrentDate()->startOfWeek()->addDays(Carbon::FRIDAY)->timestamp)->subMinutes(18)->format(self::timeFormat);
    }

    /**
     * @return string
     */
    public function getShkiah()
    {
        return $this->getSunset()->format(self::timeFormat);
    }

    /**
     * @return string
     */
    public function getTzet()
    {
        return $this->_getProportionalSunset(90 + 8)->format(self::timeFormat);
    }

    /**
     * @return string
     */
    public function getHavdala8()
    {
        return $this->_getProportionalSunset(90 + 8, $this->getCurrentDate()->startOfWeek()->addDays(Carbon::SATURDAY)->timestamp)->format(self::timeFormat);
    }

    /**
     * @return string
     */
    public function getHavdala65()
    {
        return $this->_getProportionalSunset(90 + 6.5, $this->getCurrentDate()->startOfWeek()->addDays(Carbon::SATURDAY)->timestamp)->format(self::timeFormat);
    }

    /**
     * @return string
     */
    public function getHavdala685()
    {
        return $this->_getProportionalSunset(90 + 6.85, $this->getCurrentDate()->startOfWeek()->addDays(Carbon::SATURDAY)->timestamp)->format(self::timeFormat);
    }
}
