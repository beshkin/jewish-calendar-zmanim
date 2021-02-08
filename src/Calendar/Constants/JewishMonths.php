<?php

namespace Kosherdev\Calendar\Constants;

class JewishMonths
{
    const TISHRI = [
        "id" => 1,
        "name" => "Tishri"
    ];
    const HESHVAN = [
        "id" => 2,
        "name" => "Heshvan"
    ];
    const KISLEV = [
        "id" => 3,
        "name" => "Kislev"
    ];
    const TEVET = [
        "id" => 4,
        "name" => "Tevet"
    ];
    const SHEVAT = [
        "id" => 5,
        "name" => "Shevat"
    ];
    const ADAR = [
        "id" => 7,
        "name" => "Adar"
    ];
    const ADAR_I = [
        "id" => 6,
        "name" => "Adar I"
    ];
    const ADAR_II = [
        "id" => 7,
        "name" => "Adar II"
    ];
    const NISAN = [
        "id" => 8,
        "name" => "Nisan"
    ];
    const IYAR = [
        "id" => 9,
        "name" => "Iyar"
    ];
    const SIVAN = [
        "id" => 10,
        "name" => "Sivan"
    ];
    const TAMMUZ = [
        "id" => 11,
        "name" => "Tammuz"
    ];
    const AV = [
        "id" => 12,
        "name" => "Av"
    ];
    const ELUL = [
        "id" => 13,
        "name" => "Elul"
    ];

    const NON_LEAP_YEAR = [
        self::TISHRI,
        self::HESHVAN,
        self::KISLEV,
        self::TEVET,
        self::SHEVAT,
        self::ADAR,
        self::ADAR,
        self::NISAN,
        self::IYAR,
        self::SIVAN,
        self::TAMMUZ,
        self::AV,
        self::ELUL
    ];

    const LEAP_YEAR = [
        self::TISHRI,
        self::HESHVAN,
        self::KISLEV,
        self::TEVET,
        self::SHEVAT,
        self::ADAR_I,
        self::ADAR_II,
        self::NISAN,
        self::IYAR,
        self::SIVAN,
        self::TAMMUZ,
        self::AV,
        self::ELUL
    ];
}
