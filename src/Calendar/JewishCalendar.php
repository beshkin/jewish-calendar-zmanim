<?php


namespace Kosherdev\Calendar;


use Carbon\Carbon;
use Kosherdev\Calendar\Constants\JewishHolidays;
use Kosherdev\Calendar\Constants\JewishMonths;

class JewishCalendar
{
    /**
     * Get Jewish holidays for current day
     *
     * @param Carbon $now
     * @param bool $isDiaspora
     * @param bool $postponeShushanPurimOnSaturday
     * @return array
     */
    public static function getJewishHolidays(Carbon $now, $isDiaspora = true, $postponeShushanPurimOnSaturday = false): array
    {
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
        if (self::isJewishLeapYear($jewishYear))
            return JewishMonths::LEAP_YEAR[$jewishMonth - 1]['name'];
        else
            return JewishMonths::NON_LEAP_YEAR[$jewishMonth - 1]['name'];
    }

    /**
     * Get a list of holidays falling on a current day in Julian calendar
     *
     * @param int $jdCurrent
     * @param bool $isDiaspora
     * @param bool $postponeShushanPurimOnSaturday
     * @return array
     */
    public static function getJewishHolidaysFromJulian($jdCurrent, $isDiaspora = true, $postponeShushanPurimOnSaturday = false): array
    {
        $result = [];

        $jewishDate = jdtojewish($jdCurrent);
        list($jewishMonth, $jewishDay, $jewishYear) = preg_split('/\//', $jewishDate);

        // Holidays in Elul
        if ($jewishDay == 29 && $jewishMonth == JewishMonths::ELUL['id'])
            $result[] = JewishHolidays::EREV_ROSH_HASHANAH;

        // Holidays in Tishri
        if ($jewishDay == 1 && $jewishMonth == JewishMonths::TISHRI['id'])
            $result[] = JewishHolidays::ROSH_HASHANAH_I;
        if ($jewishDay == 2 && $jewishMonth == JewishMonths::TISHRI['id'])
            $result[] = JewishHolidays::ROSH_HASHANAH_II;
        $jd = jewishtojd(JewishMonths::TISHRI['id'], 3, $jewishYear);
        $weekdayNo = jddayofweek($jd, 0);
        if ($weekdayNo == Carbon::SATURDAY) { // If the 3 Tishri would fall on Saturday ...
            // ... postpone Tzom Gedaliah to Sunday
            if ($jewishDay == 4 && $jewishMonth == JewishMonths::TISHRI['id'])
                $result[] = JewishHolidays::TZOM_GEDALIAH;
        } else {
            if ($jewishDay == 3 && $jewishMonth == JewishMonths::TISHRI['id'])
                $result[] = JewishHolidays::TZOM_GEDALIAH;
        }
        if ($jewishDay == 9 && $jewishMonth == JewishMonths::TISHRI['id'])
            $result[] = JewishHolidays::EREV_YOM_KIPPUR;
        if ($jewishDay == 10 && $jewishMonth == JewishMonths::TISHRI['id'])
            $result[] = JewishHolidays::YOM_KIPPUR;
        if ($jewishDay == 14 && $jewishMonth == JewishMonths::TISHRI['id'])
            $result[] = JewishHolidays::EREV_SUKKOT;
        if ($jewishDay == 15 && $jewishMonth == JewishMonths::TISHRI['id'])
            $result[] = JewishHolidays::SUKKOT_I;
        if ($jewishDay == 16 && $jewishMonth == JewishMonths::TISHRI['id'] && $isDiaspora)
            $result[] = JewishHolidays::SUKKOT_II;
        if ($isDiaspora) {
            if ($jewishDay >= 17 && $jewishDay <= 20 && $jewishMonth == JewishMonths::TISHRI['id'])
                $result[] = JewishHolidays::HOL_HAMOED_SUKKOT;
        } else {
            if ($jewishDay >= 16 && $jewishDay <= 20 && $jewishMonth == JewishMonths::TISHRI['id'])
                $result[] = JewishHolidays::HOL_HAMOED_SUKKOT;
        }
        if ($jewishDay == 21 && $jewishMonth == JewishMonths::TISHRI['id'])
            $result[] = JewishHolidays::HOSHANA_RABBAH;
        if ($isDiaspora) {
            if ($jewishDay == 22 && $jewishMonth == JewishMonths::TISHRI['id'])
                $result[] = JewishHolidays::SHEMINI_AZERET;
            if ($jewishDay == 23 && $jewishMonth == JewishMonths::TISHRI['id'])
                $result[] = JewishHolidays::SIMCHAT_TORAH;
            if ($jewishDay == 24 && $jewishMonth == JewishMonths::TISHRI['id'])
                $result[] = JewishHolidays::ISRU_HAG;
        } else {
            if ($jewishDay == 22 && $jewishMonth == JewishMonths::TISHRI['id'])
                $result[] = JewishHolidays::SHEMINI_AZERET_SIMCHAT_TORAH;
            if ($jewishDay == 23 && $jewishMonth == JewishMonths::TISHRI['id'])
                $result[] = JewishHolidays::ISRU_HAG;
        }

        // Holidays in Kislev/Tevet
        $hanukkahStart = jewishtojd(JewishMonths::KISLEV['id'], 25, $jewishYear);
        $hanukkahNo = (int)($jdCurrent - $hanukkahStart + 1);
        if ($hanukkahNo == 1) $result[] = JewishHolidays::HANUKKAH_I;
        if ($hanukkahNo == 2) $result[] = JewishHolidays::HANUKKAH_II;
        if ($hanukkahNo == 3) $result[] = JewishHolidays::HANUKKAH_III;
        if ($hanukkahNo == 4) $result[] = JewishHolidays::HANUKKAH_IV;
        if ($hanukkahNo == 5) $result[] = JewishHolidays::HANUKKAH_V;
        if ($hanukkahNo == 6) $result[] = JewishHolidays::HANUKKAH_VI;
        if ($hanukkahNo == 7) $result[] = JewishHolidays::HANUKKAH_VII;
        if ($hanukkahNo == 8) $result[] = JewishHolidays::HANUKKAH_VIII;

        // Holidays in Tevet
        $jd = jewishtojd(JewishMonths::TEVET['id'], 10, $jewishYear);
        $weekdayNo = jddayofweek($jd, 0);
        if ($weekdayNo == Carbon::SATURDAY) { // If the 10 Tevet would fall on Saturday ...
            // ... postpone Tzom Tevet to Sunday
            if ($jewishDay == 11 && $jewishMonth == JewishMonths::TEVET['id'])
                $result[] = JewishHolidays::TZOM_TEVET;
        } else {
            if ($jewishDay == 10 && $jewishMonth == JewishMonths::TEVET['id'])
                $result[] = JewishHolidays::TZOM_TEVET;
        }

        // Holidays in Shevat
        if ($jewishDay == 15 && $jewishMonth == JewishMonths::SHEVAT['id'])
            $result[] = JewishHolidays::TU_BSHEVAT;

        // Holidays in Adar I
        if (self::isJewishLeapYear($jewishYear) && $jewishDay == 14 && $jewishMonth == JewishMonths::ADAR_I['id'])
            $result[] = JewishHolidays::PURIM_KATAN;
        if (self::isJewishLeapYear($jewishYear) && $jewishDay == 15 && $jewishMonth == JewishMonths::ADAR_I['id'])
            $result[] = JewishHolidays::SHUSHAN_PURIM_KATAN;

        // Holidays in Adar or Adar II
        if (self::isJewishLeapYear($jewishYear))
            $purimMonth = JewishMonths::ADAR_II['id'];
        else
            $purimMonth = JewishMonths::ADAR['id'];
        $jd = jewishtojd($purimMonth, 13, $jewishYear);
        $weekdayNo = jddayofweek($jd, 0);
        if ($weekdayNo == Carbon::SATURDAY) { // If the 13 Adar or Adar II would fall on Saturday ...
            // ... move Ta'anit Esther to the preceding Thursday
            if ($jewishDay == 11 && $jewishMonth == $purimMonth)
                $result[] = JewishHolidays::TAANITH_ESTER;
        } else {
            if ($jewishDay == 13 && $jewishMonth == $purimMonth)
                $result[] = JewishHolidays::TAANITH_ESTER;
        }
        if ($jewishDay == 14 && $jewishMonth == $purimMonth)
            $result[] = JewishHolidays::PURIM;
        if ($postponeShushanPurimOnSaturday) {
            $jd = jewishtojd($purimMonth, 15, $jewishYear);
            $weekdayNo = jddayofweek($jd, 0);
            if ($weekdayNo == Carbon::SATURDAY) { // If the 15 Adar or Adar II would fall on Saturday ...
                // ... postpone Shushan Purim to Sunday
                if ($jewishDay == 16 && $jewishMonth == $purimMonth)
                    $result[] = JewishHolidays::SHUSHAN_PURIM;
            } else {
                if ($jewishDay == 15 && $jewishMonth == $purimMonth)
                    $result[] = JewishHolidays::SHUSHAN_PURIM;
            }
        } else {
            if ($jewishDay == 15 && $jewishMonth == $purimMonth)
                $result[] = JewishHolidays::SHUSHAN_PURIM;
        }

        // Holidays in Nisan
        $shabbatHagadolDay = 14;
        $jd = jewishtojd(JewishMonths::NISAN['id'], $shabbatHagadolDay, $jewishYear);
        while (jddayofweek($jd, 0) != Carbon::SATURDAY) {
            $jd--;
            $shabbatHagadolDay--;
        }
        if ($jewishDay == $shabbatHagadolDay && $jewishMonth == JewishMonths::NISAN['id'])
            $result[] = JewishHolidays::SHABBAT_HAGADOL;
        if ($jewishDay == 14 && $jewishMonth == JewishMonths::NISAN['id'])
            $result[] = JewishHolidays::EREV_PESACH;
        if ($jewishDay == 15 && $jewishMonth == JewishMonths::NISAN['id'])
            $result[] = JewishHolidays::PESACH_I;
        if ($jewishDay == 16 && $jewishMonth == JewishMonths::NISAN['id'] && $isDiaspora)
            $result[] = JewishHolidays::PESACH_II;
        if ($isDiaspora) {
            if ($jewishDay >= 17 && $jewishDay <= 20 && $jewishMonth == JewishMonths::NISAN['id'])
                $result[] = JewishHolidays::HOL_HAMOED_PESACH;
        } else {
            if ($jewishDay >= 16 && $jewishDay <= 20 && $jewishMonth == JewishMonths::NISAN['id'])
                $result[] = JewishHolidays::HOL_HAMOED_PESACH;
        }
        if ($jewishDay == 21 && $jewishMonth == JewishMonths::NISAN['id'])
            $result[] = JewishHolidays::PESACH_VII;
        if ($jewishDay == 22 && $jewishMonth == JewishMonths::NISAN['id'] && $isDiaspora)
            $result[] = JewishHolidays::PESACH_VIII;
        if ($isDiaspora) {
            if ($jewishDay == 23 && $jewishMonth == JewishMonths::NISAN['id'])
                $result[] = JewishHolidays::ISRU_HAG;
        } else {
            if ($jewishDay == 22 && $jewishMonth == JewishMonths::NISAN['id'])
                $result[] = JewishHolidays::ISRU_HAG;
        }

        $jd = jewishtojd(JewishMonths::NISAN['id'], 27, $jewishYear);
        $weekdayNo = jddayofweek($jd, 0);
        if ($weekdayNo == Carbon::FRIDAY) { // If the 27 Nisan would fall on Friday ...
            // ... then Yom Hashoah falls on Thursday
            if ($jewishDay == 26 && $jewishMonth == JewishMonths::NISAN['id'])
                $result[] = JewishHolidays::YOM_HASHOAH;
        } else {
            if ($jewishYear >= 5757) { // Since 1997 (5757) ...
                if ($weekdayNo == Carbon::SUNDAY) { // If the 27 Nisan would fall on Friday ...
                    // ... then Yom Hashoah falls on Thursday
                    if ($jewishDay == 28 && $jewishMonth == JewishMonths::NISAN['id'])
                        $result[] = JewishHolidays::YOM_HASHOAH;
                } else {
                    if ($jewishDay == 27 && $jewishMonth == JewishMonths::NISAN['id'])
                        $result[] = JewishHolidays::YOM_HASHOAH;
                }
            } else {
                if ($jewishDay == 27 && $jewishMonth == JewishMonths::NISAN['id'])
                    $result[] = JewishHolidays::YOM_HASHOAH;
            }
        }

        // Holidays in Iyar

        $jd = jewishtojd(JewishMonths::IYAR['id'], 4, $jewishYear);
        $weekdayNo = jddayofweek($jd, 0);

        // If the 4 Iyar would fall on Friday or Thursday ...
        // ... then Yom Hazikaron falls on Wednesday and Yom Ha'Atzmaut on Thursday
        if ($weekdayNo == Carbon::FRIDAY) {
            if ($jewishDay == 2 && $jewishMonth == JewishMonths::IYAR['id'])
                $result[] = JewishHolidays::YOM_HAZIKARON;
            if ($jewishDay == 3 && $jewishMonth == JewishMonths::IYAR['id'])
                $result[] = JewishHolidays::YOM_HAATZMAUT;
        } else {
            if ($weekdayNo == Carbon::THURSDAY) {
                if ($jewishDay == 3 && $jewishMonth == JewishMonths::IYAR['id'])
                    $result[] = JewishHolidays::YOM_HAZIKARON;
                if ($jewishDay == 4 && $jewishMonth == JewishMonths::IYAR['id'])
                    $result[] = JewishHolidays::YOM_HAATZMAUT;
            } else {
                if ($jewishYear >= 5764) { // Since 2004 (5764) ...
                    if ($weekdayNo == Carbon::SUNDAY) { // If the 4 Iyar would fall on Sunday ...
                        // ... then Yom Hazicaron falls on Monday
                        if ($jewishDay == 5 && $jewishMonth == JewishMonths::IYAR['id'])
                            $result[] = JewishHolidays::YOM_HAZIKARON;
                        if ($jewishDay == 6 && $jewishMonth == JewishMonths::IYAR['id'])
                            $result[] = JewishHolidays::YOM_HAATZMAUT;
                    } else {
                        if ($jewishDay == 4 && $jewishMonth == JewishMonths::IYAR['id'])
                            $result[] = JewishHolidays::YOM_HAZIKARON;
                        if ($jewishDay == 5 && $jewishMonth == JewishMonths::IYAR['id'])
                            $result[] = JewishHolidays::YOM_HAATZMAUT;
                    }
                } else {
                    if ($jewishDay == 4 && $jewishMonth == JewishMonths::IYAR['id'])
                        $result[] = JewishHolidays::YOM_HAZIKARON;
                    if ($jewishDay == 5 && $jewishMonth == JewishMonths::IYAR['id'])
                        $result[] = JewishHolidays::YOM_HAATZMAUT;
                }
            }
        }

        if ($jewishDay == 14 && $jewishMonth == JewishMonths::IYAR['id'])
            $result[] = JewishHolidays::PESACH_SHEINI;
        if ($jewishDay == 18 && $jewishMonth == JewishMonths::IYAR['id'])
            $result[] = JewishHolidays::LAG_BOMER;
        if ($jewishDay == 28 && $jewishMonth == JewishMonths::IYAR['id'])
            $result[] = JewishHolidays::YOM_YERUSHALAYIM;

        // Holidays in Sivan
        if ($jewishDay == 5 && $jewishMonth == JewishMonths::SIVAN['id'])
            $result[] = JewishHolidays::EREV_SHAVUOT;
        if ($jewishDay == 6 && $jewishMonth == JewishMonths::SIVAN['id'])
            $result[] = JewishHolidays::SHAVUOT_I;
        if ($jewishDay == 7 && $jewishMonth == JewishMonths::SIVAN['id'] && $isDiaspora)
            $result[] = JewishHolidays::SHAVUOT_II;
        if ($isDiaspora) {
            if ($jewishDay == 8 && $jewishMonth == JewishMonths::SIVAN['id'])
                $result[] = JewishHolidays::ISRU_HAG;
        } else {
            if ($jewishDay == 7 && $jewishMonth == JewishMonths::SIVAN['id'])
                $result[] = JewishHolidays::ISRU_HAG;
        }

        // Holidays in Tammuz
        $jd = jewishtojd(JewishMonths::SIVAN['id'], 17, $jewishYear);
        $weekdayNo = jddayofweek($jd, 0);
        if ($weekdayNo == Carbon::SATURDAY) { // If the 17 Tammuz would fall on Saturday ...
            // ... postpone Tzom Tammuz to Sunday
            if ($jewishDay == 18 && $jewishMonth == JewishMonths::SIVAN['id'])
                $result[] = JewishHolidays::TZOM_TAMMUZ;
        } else {
            if ($jewishDay == 17 && $jewishMonth == JewishMonths::SIVAN['id'])
                $result[] = JewishHolidays::TZOM_TAMMUZ;
        }

        // Holidays in Av
        $jd = jewishtojd(JewishMonths::AV['id'], 9, $jewishYear);
        $weekdayNo = jddayofweek($jd, 0);
        if ($weekdayNo == Carbon::SATURDAY) { // If the 9 Av would fall on Saturday ...
            // ... postpone Tisha B'Av to Sunday
            if ($jewishDay == 10 && $jewishMonth == JewishMonths::AV['id'])
                $result[] = JewishHolidays::TISHA_BAV;
        } else {
            if ($jewishDay == 9 && $jewishMonth == JewishMonths::AV['id'])
                $result[] = JewishHolidays::TISHA_BAV;
        }
        if ($jewishDay == 15 && $jewishMonth == JewishMonths::AV['id'])
            $result[] = JewishHolidays::TU_BAV;

        return $result;
    }


}
