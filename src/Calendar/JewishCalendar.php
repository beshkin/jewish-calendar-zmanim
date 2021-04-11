<?php


namespace Kosherdev\Calendar;


use Carbon\Carbon;
use Kosherdev\Calendar\Constants\JewishHolidays;
use Kosherdev\Calendar\Constants\JewishMonths;
use Kosherdev\Calendar\Constants\JewishWeekDays;
use Kosherdev\Calendar\Constants\JewishYearMap;
use Kosherdev\Calendar\Constants\TorahParshioys;

class JewishCalendar
{
    const inIsrael = false;
    const hebrewFormat = false;

    /**
     * Get Jewish holidays for current day
     *
     * @param Carbon $now
     * @param bool $isDiaspora
     * @param bool $postponeShushanPurimOnSaturday
     * @return array
     */
    public static function getJewishHolidays(
        Carbon $now,
        $isDiaspora = true,
        $postponeShushanPurimOnSaturday = false
    ): array {
        $jdCurrent = gregoriantojd($now->month, $now->day, $now->year);
        return self::getJewishHolidaysFromJulian($jdCurrent, $isDiaspora, $postponeShushanPurimOnSaturday);
    }

    /**
     * Check if current Jewish year is leap
     *
     * @param $year
     * @return bool
     */
    public static function isJewishLeapYear($year): bool
    {
        if ($year % 19 == 0 || $year % 19 == 3 || $year % 19 == 6 ||
            $year % 19 == 8 || $year % 19 == 11 || $year % 19 == 14 ||
            $year % 19 == 17) {
            return true;
        }
        return false;
    }

    /**
     * Get the name of current Jewish month
     *
     * @param $jewishMonth
     * @param $jewishYear
     * @return mixed
     */
    public static function getJewishMonthName($jewishMonth, $jewishYear): string
    {
        if (self::isJewishLeapYear($jewishYear)) {
            return JewishMonths::LEAP_YEAR[$jewishMonth - 1]['name'];
        } else {
            return JewishMonths::NON_LEAP_YEAR[$jewishMonth - 1]['name'];
        }
    }

    /**
     * Get a list of holidays falling on a current day in Julian calendar
     *
     * @param int $jdCurrent
     * @param bool $isDiaspora
     * @param bool $postponeShushanPurimOnSaturday
     * @return array
     */
    public static function getJewishHolidaysFromJulian(
        $jdCurrent,
        $isDiaspora = true,
        $postponeShushanPurimOnSaturday = false
    ): array {
        $result = [];

        $jewishDate = jdtojewish($jdCurrent);
        list($jewishMonth, $jewishDay, $jewishYear) = preg_split('/\//', $jewishDate);

        // Holidays in Elul
        if ($jewishDay == 29 && $jewishMonth == JewishMonths::ELUL['id']) {
            $result[] = JewishHolidays::EREV_ROSH_HASHANAH;
        }

        // Holidays in Tishri
        if ($jewishDay == 1 && $jewishMonth == JewishMonths::TISHRI['id']) {
            $result[] = JewishHolidays::ROSH_HASHANAH_I;
        }
        if ($jewishDay == 2 && $jewishMonth == JewishMonths::TISHRI['id']) {
            $result[] = JewishHolidays::ROSH_HASHANAH_II;
        }
        $jd = jewishtojd(JewishMonths::TISHRI['id'], 3, $jewishYear);
        $weekdayNo = jddayofweek($jd, 0);
        if ($weekdayNo == Carbon::SATURDAY) { // If the 3 Tishri would fall on Saturday ...
            // ... postpone Tzom Gedaliah to Sunday
            if ($jewishDay == 4 && $jewishMonth == JewishMonths::TISHRI['id']) {
                $result[] = JewishHolidays::TZOM_GEDALIAH;
            }
        } else {
            if ($jewishDay == 3 && $jewishMonth == JewishMonths::TISHRI['id']) {
                $result[] = JewishHolidays::TZOM_GEDALIAH;
            }
        }
        if ($jewishDay == 9 && $jewishMonth == JewishMonths::TISHRI['id']) {
            $result[] = JewishHolidays::EREV_YOM_KIPPUR;
        }
        if ($jewishDay == 10 && $jewishMonth == JewishMonths::TISHRI['id']) {
            $result[] = JewishHolidays::YOM_KIPPUR;
        }
        if ($jewishDay == 14 && $jewishMonth == JewishMonths::TISHRI['id']) {
            $result[] = JewishHolidays::EREV_SUKKOT;
        }
        if ($jewishDay == 15 && $jewishMonth == JewishMonths::TISHRI['id']) {
            $result[] = JewishHolidays::SUKKOT_I;
        }
        if ($jewishDay == 16 && $jewishMonth == JewishMonths::TISHRI['id'] && $isDiaspora) {
            $result[] = JewishHolidays::SUKKOT_II;
        }
        if ($isDiaspora) {
            if ($jewishDay >= 17 && $jewishDay <= 20 && $jewishMonth == JewishMonths::TISHRI['id']) {
                $result[] = JewishHolidays::HOL_HAMOED_SUKKOT;
            }
        } else {
            if ($jewishDay >= 16 && $jewishDay <= 20 && $jewishMonth == JewishMonths::TISHRI['id']) {
                $result[] = JewishHolidays::HOL_HAMOED_SUKKOT;
            }
        }
        if ($jewishDay == 21 && $jewishMonth == JewishMonths::TISHRI['id']) {
            $result[] = JewishHolidays::HOSHANA_RABBAH;
        }
        if ($isDiaspora) {
            if ($jewishDay == 22 && $jewishMonth == JewishMonths::TISHRI['id']) {
                $result[] = JewishHolidays::SHEMINI_AZERET;
            }
            if ($jewishDay == 23 && $jewishMonth == JewishMonths::TISHRI['id']) {
                $result[] = JewishHolidays::SIMCHAT_TORAH;
            }
            if ($jewishDay == 24 && $jewishMonth == JewishMonths::TISHRI['id']) {
                $result[] = JewishHolidays::ISRU_HAG;
            }
        } else {
            if ($jewishDay == 22 && $jewishMonth == JewishMonths::TISHRI['id']) {
                $result[] = JewishHolidays::SHEMINI_AZERET_SIMCHAT_TORAH;
            }
            if ($jewishDay == 23 && $jewishMonth == JewishMonths::TISHRI['id']) {
                $result[] = JewishHolidays::ISRU_HAG;
            }
        }

        // Holidays in Kislev/Tevet
        $hanukkahStart = jewishtojd(JewishMonths::KISLEV['id'], 25, $jewishYear);
        $hanukkahNo = (int)($jdCurrent - $hanukkahStart + 1);
        if ($hanukkahNo == 1) {
            $result[] = JewishHolidays::HANUKKAH_I;
        }
        if ($hanukkahNo == 2) {
            $result[] = JewishHolidays::HANUKKAH_II;
        }
        if ($hanukkahNo == 3) {
            $result[] = JewishHolidays::HANUKKAH_III;
        }
        if ($hanukkahNo == 4) {
            $result[] = JewishHolidays::HANUKKAH_IV;
        }
        if ($hanukkahNo == 5) {
            $result[] = JewishHolidays::HANUKKAH_V;
        }
        if ($hanukkahNo == 6) {
            $result[] = JewishHolidays::HANUKKAH_VI;
        }
        if ($hanukkahNo == 7) {
            $result[] = JewishHolidays::HANUKKAH_VII;
        }
        if ($hanukkahNo == 8) {
            $result[] = JewishHolidays::HANUKKAH_VIII;
        }

        // Holidays in Tevet
        $jd = jewishtojd(JewishMonths::TEVET['id'], 10, $jewishYear);
        $weekdayNo = jddayofweek($jd, 0);
        if ($weekdayNo == Carbon::SATURDAY) { // If the 10 Tevet would fall on Saturday ...
            // ... postpone Tzom Tevet to Sunday
            if ($jewishDay == 11 && $jewishMonth == JewishMonths::TEVET['id']) {
                $result[] = JewishHolidays::TZOM_TEVET;
            }
        } else {
            if ($jewishDay == 10 && $jewishMonth == JewishMonths::TEVET['id']) {
                $result[] = JewishHolidays::TZOM_TEVET;
            }
        }

        // Holidays in Shevat
        if ($jewishDay == 15 && $jewishMonth == JewishMonths::SHEVAT['id']) {
            $result[] = JewishHolidays::TU_BSHEVAT;
        }

        // Holidays in Adar I
        if (self::isJewishLeapYear($jewishYear) && $jewishDay == 14 && $jewishMonth == JewishMonths::ADAR_I['id']) {
            $result[] = JewishHolidays::PURIM_KATAN;
        }
        if (self::isJewishLeapYear($jewishYear) && $jewishDay == 15 && $jewishMonth == JewishMonths::ADAR_I['id']) {
            $result[] = JewishHolidays::SHUSHAN_PURIM_KATAN;
        }

        // Holidays in Adar or Adar II
        if (self::isJewishLeapYear($jewishYear)) {
            $purimMonth = JewishMonths::ADAR_II['id'];
        } else {
            $purimMonth = JewishMonths::ADAR['id'];
        }
        $jd = jewishtojd($purimMonth, 13, $jewishYear);
        $weekdayNo = jddayofweek($jd, 0);
        if ($weekdayNo == Carbon::SATURDAY) { // If the 13 Adar or Adar II would fall on Saturday ...
            // ... move Ta'anit Esther to the preceding Thursday
            if ($jewishDay == 11 && $jewishMonth == $purimMonth) {
                $result[] = JewishHolidays::TAANITH_ESTER;
            }
        } else {
            if ($jewishDay == 13 && $jewishMonth == $purimMonth) {
                $result[] = JewishHolidays::TAANITH_ESTER;
            }
        }
        if ($jewishDay == 14 && $jewishMonth == $purimMonth) {
            $result[] = JewishHolidays::PURIM;
        }
        if ($postponeShushanPurimOnSaturday) {
            $jd = jewishtojd($purimMonth, 15, $jewishYear);
            $weekdayNo = jddayofweek($jd, 0);
            if ($weekdayNo == Carbon::SATURDAY) { // If the 15 Adar or Adar II would fall on Saturday ...
                // ... postpone Shushan Purim to Sunday
                if ($jewishDay == 16 && $jewishMonth == $purimMonth) {
                    $result[] = JewishHolidays::SHUSHAN_PURIM;
                }
            } else {
                if ($jewishDay == 15 && $jewishMonth == $purimMonth) {
                    $result[] = JewishHolidays::SHUSHAN_PURIM;
                }
            }
        } else {
            if ($jewishDay == 15 && $jewishMonth == $purimMonth) {
                $result[] = JewishHolidays::SHUSHAN_PURIM;
            }
        }

        // Holidays in Nisan
        $shabbatHagadolDay = 14;
        $jd = jewishtojd(JewishMonths::NISAN['id'], $shabbatHagadolDay, $jewishYear);
        while (jddayofweek($jd, 0) != Carbon::SATURDAY) {
            $jd--;
            $shabbatHagadolDay--;
        }
        if ($jewishDay == $shabbatHagadolDay && $jewishMonth == JewishMonths::NISAN['id']) {
            $result[] = JewishHolidays::SHABBAT_HAGADOL;
        }
        if ($jewishDay == 14 && $jewishMonth == JewishMonths::NISAN['id']) {
            $result[] = JewishHolidays::EREV_PESACH;
        }
        if ($jewishDay == 15 && $jewishMonth == JewishMonths::NISAN['id']) {
            $result[] = JewishHolidays::PESACH_I;
        }
        if ($jewishDay == 16 && $jewishMonth == JewishMonths::NISAN['id'] && $isDiaspora) {
            $result[] = JewishHolidays::PESACH_II;
        }
        if ($isDiaspora) {
            if ($jewishDay >= 17 && $jewishDay <= 20 && $jewishMonth == JewishMonths::NISAN['id']) {
                $result[] = JewishHolidays::HOL_HAMOED_PESACH;
            }
        } else {
            if ($jewishDay >= 16 && $jewishDay <= 20 && $jewishMonth == JewishMonths::NISAN['id']) {
                $result[] = JewishHolidays::HOL_HAMOED_PESACH;
            }
        }
        if ($jewishDay == 21 && $jewishMonth == JewishMonths::NISAN['id']) {
            $result[] = JewishHolidays::PESACH_VII;
        }
        if ($jewishDay == 22 && $jewishMonth == JewishMonths::NISAN['id'] && $isDiaspora) {
            $result[] = JewishHolidays::PESACH_VIII;
        }
        if ($isDiaspora) {
            if ($jewishDay == 23 && $jewishMonth == JewishMonths::NISAN['id']) {
                $result[] = JewishHolidays::ISRU_HAG;
            }
        } else {
            if ($jewishDay == 22 && $jewishMonth == JewishMonths::NISAN['id']) {
                $result[] = JewishHolidays::ISRU_HAG;
            }
        }

        $jd = jewishtojd(JewishMonths::NISAN['id'], 27, $jewishYear);
        $weekdayNo = jddayofweek($jd, 0);
        if ($weekdayNo == Carbon::FRIDAY) { // If the 27 Nisan would fall on Friday ...
            // ... then Yom Hashoah falls on Thursday
            if ($jewishDay == 26 && $jewishMonth == JewishMonths::NISAN['id']) {
                $result[] = JewishHolidays::YOM_HASHOAH;
            }
        } else {
            if ($jewishYear >= 5757) { // Since 1997 (5757) ...
                if ($weekdayNo == Carbon::SUNDAY) { // If the 27 Nisan would fall on Friday ...
                    // ... then Yom Hashoah falls on Thursday
                    if ($jewishDay == 28 && $jewishMonth == JewishMonths::NISAN['id']) {
                        $result[] = JewishHolidays::YOM_HASHOAH;
                    }
                } else {
                    if ($jewishDay == 27 && $jewishMonth == JewishMonths::NISAN['id']) {
                        $result[] = JewishHolidays::YOM_HASHOAH;
                    }
                }
            } else {
                if ($jewishDay == 27 && $jewishMonth == JewishMonths::NISAN['id']) {
                    $result[] = JewishHolidays::YOM_HASHOAH;
                }
            }
        }

        // Holidays in Iyar

        $jd = jewishtojd(JewishMonths::IYAR['id'], 4, $jewishYear);
        $weekdayNo = jddayofweek($jd, 0);

        // If the 4 Iyar would fall on Friday or Thursday ...
        // ... then Yom Hazikaron falls on Wednesday and Yom Ha'Atzmaut on Thursday
        if ($weekdayNo == Carbon::FRIDAY) {
            if ($jewishDay == 2 && $jewishMonth == JewishMonths::IYAR['id']) {
                $result[] = JewishHolidays::YOM_HAZIKARON;
            }
            if ($jewishDay == 3 && $jewishMonth == JewishMonths::IYAR['id']) {
                $result[] = JewishHolidays::YOM_HAATZMAUT;
            }
        } else {
            if ($weekdayNo == Carbon::THURSDAY) {
                if ($jewishDay == 3 && $jewishMonth == JewishMonths::IYAR['id']) {
                    $result[] = JewishHolidays::YOM_HAZIKARON;
                }
                if ($jewishDay == 4 && $jewishMonth == JewishMonths::IYAR['id']) {
                    $result[] = JewishHolidays::YOM_HAATZMAUT;
                }
            } else {
                if ($jewishYear >= 5764) { // Since 2004 (5764) ...
                    if ($weekdayNo == Carbon::SUNDAY) { // If the 4 Iyar would fall on Sunday ...
                        // ... then Yom Hazicaron falls on Monday
                        if ($jewishDay == 5 && $jewishMonth == JewishMonths::IYAR['id']) {
                            $result[] = JewishHolidays::YOM_HAZIKARON;
                        }
                        if ($jewishDay == 6 && $jewishMonth == JewishMonths::IYAR['id']) {
                            $result[] = JewishHolidays::YOM_HAATZMAUT;
                        }
                    } else {
                        if ($jewishDay == 4 && $jewishMonth == JewishMonths::IYAR['id']) {
                            $result[] = JewishHolidays::YOM_HAZIKARON;
                        }
                        if ($jewishDay == 5 && $jewishMonth == JewishMonths::IYAR['id']) {
                            $result[] = JewishHolidays::YOM_HAATZMAUT;
                        }
                    }
                } else {
                    if ($jewishDay == 4 && $jewishMonth == JewishMonths::IYAR['id']) {
                        $result[] = JewishHolidays::YOM_HAZIKARON;
                    }
                    if ($jewishDay == 5 && $jewishMonth == JewishMonths::IYAR['id']) {
                        $result[] = JewishHolidays::YOM_HAATZMAUT;
                    }
                }
            }
        }

        if ($jewishDay == 14 && $jewishMonth == JewishMonths::IYAR['id']) {
            $result[] = JewishHolidays::PESACH_SHEINI;
        }
        if ($jewishDay == 18 && $jewishMonth == JewishMonths::IYAR['id']) {
            $result[] = JewishHolidays::LAG_BOMER;
        }
        if ($jewishDay == 28 && $jewishMonth == JewishMonths::IYAR['id']) {
            $result[] = JewishHolidays::YOM_YERUSHALAYIM;
        }

        // Holidays in Sivan
        if ($jewishDay == 5 && $jewishMonth == JewishMonths::SIVAN['id']) {
            $result[] = JewishHolidays::EREV_SHAVUOT;
        }
        if ($jewishDay == 6 && $jewishMonth == JewishMonths::SIVAN['id']) {
            $result[] = JewishHolidays::SHAVUOT_I;
        }
        if ($jewishDay == 7 && $jewishMonth == JewishMonths::SIVAN['id'] && $isDiaspora) {
            $result[] = JewishHolidays::SHAVUOT_II;
        }
        if ($isDiaspora) {
            if ($jewishDay == 8 && $jewishMonth == JewishMonths::SIVAN['id']) {
                $result[] = JewishHolidays::ISRU_HAG;
            }
        } else {
            if ($jewishDay == 7 && $jewishMonth == JewishMonths::SIVAN['id']) {
                $result[] = JewishHolidays::ISRU_HAG;
            }
        }

        // Holidays in Tammuz
        $jd = jewishtojd(JewishMonths::SIVAN['id'], 17, $jewishYear);
        $weekdayNo = jddayofweek($jd, 0);
        if ($weekdayNo == Carbon::SATURDAY) { // If the 17 Tammuz would fall on Saturday ...
            // ... postpone Tzom Tammuz to Sunday
            if ($jewishDay == 18 && $jewishMonth == JewishMonths::SIVAN['id']) {
                $result[] = JewishHolidays::TZOM_TAMMUZ;
            }
        } else {
            if ($jewishDay == 17 && $jewishMonth == JewishMonths::SIVAN['id']) {
                $result[] = JewishHolidays::TZOM_TAMMUZ;
            }
        }

        // Holidays in Av
        $jd = jewishtojd(JewishMonths::AV['id'], 9, $jewishYear);
        $weekdayNo = jddayofweek($jd, 0);
        if ($weekdayNo == Carbon::SATURDAY) { // If the 9 Av would fall on Saturday ...
            // ... postpone Tisha B'Av to Sunday
            if ($jewishDay == 10 && $jewishMonth == JewishMonths::AV['id']) {
                $result[] = JewishHolidays::TISHA_BAV;
            }
        } else {
            if ($jewishDay == 9 && $jewishMonth == JewishMonths::AV['id']) {
                $result[] = JewishHolidays::TISHA_BAV;
            }
        }
        if ($jewishDay == 15 && $jewishMonth == JewishMonths::AV['id']) {
            $result[] = JewishHolidays::TU_BAV;
        }

        return $result;
    }

    /**
     * Reverse conversion from Jewish date to Gregorian
     *
     * @param int $jewishYear
     * @param int $month
     * @param int $day
     * @return Carbon
     */
    public static function getDateFromJewish(int $jewishYear, int $month, int $day): Carbon
    {
        $julian = jewishtojd($month, $day, $jewishYear);
        $gregorianDate = jdtogregorian($julian);

        return Carbon::parse($gregorianDate);
    }

    /**
     * Get current week Torah chapter
     *
     * @param Carbon $now
     * @return string
     */
    public static function getParsha(Carbon $now)
    {
        $index = self::getParshaIndex($now);
        return $index == -1 ? "" : (self::hebrewFormat ? TorahParshioys::hebrewParshiyos[$index] : TorahParshioys::transliteratedParshios[$index]);
    }

    /**
     * Getting index of the chapter in a year cicle
     *
     * @param Carbon $now
     * @return int|mixed
     */
    public static function getParshaIndex(Carbon $now)
    {
        if ($now->dayOfWeek != Carbon::SATURDAY) {
            return -1;
        } else {
            $array = [];
            $jewishYear = self::getJewishYear($now);
            $kvia = self::getCheshvanKislevKviah($jewishYear);
            $roshHashana = self::getDateFromJewish($jewishYear, 1, 1);

            $roshHashanaDay = $roshHashana->dayOfWeek + 1; //to fit Jewish week days
            $week = $now->diffInWeeks($roshHashana);
            if (!self::isJewishLeapYear($jewishYear)) {
                switch ($roshHashanaDay) {
                    case JewishWeekDays::YOM_SHENI:
                        if ($kvia == 0) {
                            $array = JewishYearMap::Mon_short;
                        } else {
                            if ($kvia == 2) {
                                $array = self::inIsrael ? JewishYearMap::Mon_short : JewishYearMap::Mon_long;
                            }
                        }
                        break;
                    case JewishWeekDays::YOM_SHLISHI:
                        if ($kvia == 1) {
                            $array = self::inIsrael ? JewishYearMap::Mon_short : JewishYearMap::Mon_long;
                        }
                        break;
                    case JewishWeekDays::YOM_REVII:
                    case JewishWeekDays::YOM_SHISHI:
                    default:
                        break;
                    case JewishWeekDays::YOM_CHAMISHI:
                        if ($kvia == 1) {
                            $array = self::inIsrael ? JewishYearMap::Thu_normal_Israel : JewishYearMap::Thu_normal;
                        } else {
                            if ($kvia == 2) {
                                $array = JewishYearMap::Thu_long;
                            }
                        }
                        break;
                    case JewishWeekDays::YOM_SHABBAT:
                        if ($kvia == 0) {
                            $array = JewishYearMap::Sat_short;
                        } else {
                            if ($kvia == 2) {
                                $array = JewishYearMap::Sat_long;
                            }
                        }
                }
            } else {
                switch ($roshHashanaDay) {
                    case JewishWeekDays::YOM_SHENI:
                        if ($kvia == 0) {
                            $array = self::inIsrael ? JewishYearMap::Mon_short_leap_Israel : JewishYearMap::Mon_short_leap;
                        } else {
                            if ($kvia == 2) {
                                $array = self::inIsrael ? JewishYearMap::Mon_long_leap_Israel : JewishYearMap::Mon_long_leap;
                            }
                        }
                        break;
                    case JewishWeekDays::YOM_SHLISHI:
                        if ($kvia == 1) {
                            $array = self::inIsrael ? JewishYearMap::Mon_long_leap_Israel : JewishYearMap::Mon_long_leap;
                        }
                        break;
                    case JewishWeekDays::YOM_REVII:
                    case JewishWeekDays::YOM_SHISHI:
                    default:
                        break;
                    case JewishWeekDays::YOM_CHAMISHI:
                        if ($kvia == 0) {
                            $array = JewishYearMap::Thu_short_leap;
                        } else {
                            if ($kvia == 2) {
                                $array = JewishYearMap::Thu_long_leap;
                            }
                        }
                        break;
                    case JewishWeekDays::YOM_SHABBAT:
                        if ($kvia == 0) {
                            $array = JewishYearMap::Sat_short_leap;
                        } else {
                            if ($kvia == 2) {
                                $array = self::inIsrael ? JewishYearMap::Sat_short_leap : JewishYearMap::Sat_long_leap;
                            }
                        }
                }
            }

            if (count($array) > 0) {
                return $array[$week];
            }
        }
        return -1;
    }

    /**
     * Getting Chechvan Kislev Kviah
     *
     * @param $jewishYear
     * @return int
     */
    private static function getCheshvanKislevKviah($jewishYear)
    {
        if (self::isCheshvanLong($jewishYear) && !self::isKislevShort($jewishYear)) {
            return 2;
        }

        return !self::isCheshvanLong($jewishYear) && self::isKislevShort($jewishYear) ? 0 : 1;
    }

    /**
     * Checking if Cheshvan is long this year
     *
     * @param $jewishYear
     * @return bool
     */
    private static function isCheshvanLong($jewishYear)
    {
        return self::getDaysInJewishYear($jewishYear) % 10 == 5;
    }

    /**
     * Checking if Kislev is short this year
     *
     * @param $jewishYear
     * @return bool
     */
    private static function isKislevShort($jewishYear)
    {
        return self::getDaysInJewishYear($jewishYear) % 10 == 3;
    }

    /**
     * Get Jewish year
     *
     * @param Carbon $now
     * @return int
     */
    public static function getJewishYear(Carbon $now): int
    {
        $jdCurrent = gregoriantojd($now->month, $now->day, $now->year);
        $jewishDate = jdtojewish($jdCurrent);
        list($jewishMonth, $jewishDay, $jewishYear) = preg_split('/\//', $jewishDate);
        return (int)$jewishYear;
    }

    /**
     * Get number of days in Jewish year
     *
     * @param $year
     * @return string
     */
    private static function getDaysInJewishYear($year)
    {
        return bcsub(self::getJewishCalendarElapsedDays($year + 1), self::getJewishCalendarElapsedDays($year));
    }

    /**
     * Get Jewish Calendar Elapsed days
     *
     * @param int $year
     * @return int
     */
    private static function getJewishCalendarElapsedDays(int $year)
    {
        $chalakimSince = self::getChalakimSinceMoladTohu($year, 7);

        $moladDay = bcdiv((string)$chalakimSince, 25920);
        $moladParts = bcsub($chalakimSince, bcmul($moladDay, 25920));
        return self::addDechiyos($year, $moladDay, $moladParts);
    }

    /**
     * Add Dechiyos
     *
     * @param int $year
     * @param int $moladDay
     * @param int $moladParts
     * @return int
     */
    private static function addDechiyos(int $year, int $moladDay, int $moladParts)
    {
        $roshHashanaDay = $moladDay;
        if ($moladParts >= 19440 || $moladDay % 7 == 2 && $moladParts >= 9924 && !self::isJewishLeapYear(
                $year
            ) || $moladDay % 7 == 1 && $moladParts >= 16789 && self::isJewishLeapYear($year - 1)) {
            $roshHashanaDay = $moladDay + 1;
        }

        if ($roshHashanaDay % 7 == 0 || $roshHashanaDay % 7 == 3 || $roshHashanaDay % 7 == 5) {
            $roshHashanaDay++;
        }

        return $roshHashanaDay;
    }

    /**
     * Get Chalakim Since Molad Tohu
     *
     * @param int $year
     * @param int $month
     * @return string
     */
    private static function getChalakimSinceMoladTohu(int $year, int $month)
    {
        $monthOfYear = self::getJewishMonthOfYear($year, $month);
        $monthsElapsed = (int)(235 * bcdiv(
                ($year - 1),
                19
            ) + 12 * (($year - 1) % 19) + (7 * (($year - 1) % 19) + 1) / 19 + ($monthOfYear - 1));

        return bcadd(31524, bcmul(765433, $monthsElapsed));
    }

    /**
     * Get Jewish Month of the Year
     *
     * @param int $year
     * @param int $month
     * @return int
     */
    public static function getJewishMonthOfYear(int $year, int $month)
    {
        $isLeapYear = self::isJewishLeapYear($year);
        return ($month + ($isLeapYear ? 6 : 5)) % ($isLeapYear ? 13 : 12) + 1;
    }
}
