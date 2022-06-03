<?php

namespace App\Helpers;


class TimeHelper
{
    private const YEARS = [
        'год',
        'года',
        'лет',
    ];

    private const MONTHS = [
        'месяц',
        'месяца',
        'месяцев',
    ];

    private const DAYS = [
        'день',
        'дня',
        'дней',
    ];

    private const HOURS = [
        'час',
        'часа',
        'часов',
    ];

    private const MINUTES = [
        'минута',
        'минуты',
        'минут',
    ];

    public static function getDateIntervalString(\DateInterval $dateInterval)
    {
        if ($dateInterval->d > 0) {
            return self::getDaysString($dateInterval->d);
        } elseif ($dateInterval->h > 0) {
            return self::getHoursString($dateInterval->h);
        } elseif ($dateInterval->m > 0) {
            return self::getMinutesString($dateInterval->m);
        }
    }

    /**
     * @param int $years
     * @return string
     */
    public static function getYearsString(int $years): string
    {
        return self::numWord($years, self::YEARS);
    }

    /**
     * @param int $months
     * @return string
     */
    public static function getMonthsString(int $months): string
    {
        return self::numWord($months, self::MONTHS);
    }

    /**
     * @param int $days
     * @return string
     */
    public static function getDaysString(int $days): string
    {
        return self::numWord($days, self::DAYS);
    }

    /**
     * @param int $hours
     * @return string
     */
    public static function getHoursString(int $hours): string
    {
        return self::numWord($hours, self::HOURS);
    }

    /**
     * @param int $minutes
     * @return string
     */
    public static function getMinutesString(int $minutes): string
    {
        return self::numWord($minutes, self::MINUTES);
    }

    /**
     * Склонение существительных после числительных.
     *
     * @param string $value Значение
     * @param array  $words Массив вариантов, например: array('товар', 'товара', 'товаров')
     * @param bool   $show  Включает значение $value в результирующею строку
     *
     * @return string
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public static function numWord($value, $words, $show = true): string
    {
        $num = $value % 100;
        if ($num > 19) {
            $num = $num % 10;
        }

        $out = ($show) ? $value.' ' : '';
        switch ($num) {
            case 1:
                $out .= $words[0];

                break;
            case 2:
            case 3:
            case 4:
                $out .= $words[1];

                break;
            default:
                $out .= $words[2];

                break;
        }

        return $out;
    }
}