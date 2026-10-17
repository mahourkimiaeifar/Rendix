<?php

namespace App\Support;

use Carbon\Carbon;
use Morilog\Jalali\Jalalian;
use Throwable;

class Jalali
{
    private static array $persianDigits = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];

    private static array $arabicDigits = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];

    public static function toLatinDigits(?string $value): string
    {
        $value = (string) $value;

        $value = str_replace(self::$persianDigits, ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'], $value);
        $value = str_replace(self::$arabicDigits, ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'], $value);

        return $value;
    }

    public static function toPersianDigits(string $value): string
    {
        return str_replace(
            ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'],
            self::$persianDigits,
            $value
        );
    }

    public static function parse(?string $value): ?Carbon
    {
        $value = trim(self::toLatinDigits((string) $value));

        if ($value === '') {
            return null;
        }

        $value = preg_replace('/\s+/u', ' ', $value);

        $time = null;

        if (preg_match('/(\d{1,2}):(\d{2})$/u', $value, $matches)) {
            $time = sprintf('%02d:%02d', (int) $matches[1], (int) $matches[2]);
            $value = trim(preg_replace('/\s*\d{1,2}:\d{2}$/u', '', $value));
        }

        $value = str_replace(['-', '.'], '/', $value);

        if (! preg_match('/^\d{2,4}\/\d{1,2}\/\d{1,2}$/u', $value)) {
            return null;
        }

        $parts = explode('/', $value);

        if (count($parts) !== 3) {
            return null;
        }

        $year = (int) $parts[0];
        $month = (int) $parts[1];
        $day = (int) $parts[2];

        if ($year < 100) {
            $year += 1300;
        }

        if ($year < 1000 || $month < 1 || $month > 12 || $day < 1 || $day > 31) {
            return null;
        }

        try {
            $jalaliString = sprintf('%04d/%02d/%02d', $year, $month, $day);
            $jalalian = Jalalian::fromFormat('Y/m/d', $jalaliString);
            $carbon = $jalalian->toCarbon();

            if ($time) {
                [$hour, $minute] = explode(':', $time);
                $carbon->setTime((int) $hour, (int) $minute, 0);
            }

            return $carbon;
        } catch (Throwable) {
            return null;
        }
    }

    public static function date(?Carbon $date): string
    {
        if (! $date) {
            return '';
        }

        try {
            $jalalian = Jalalian::fromCarbon($date);

            return sprintf(
                '%04d/%02d/%02d',
                $jalalian->getYear(),
                $jalalian->getMonth(),
                $jalalian->getDay()
            );
        } catch (Throwable) {
            return '';
        }
    }

    public static function dateTime(?Carbon $date): string
    {
        if (! $date) {
            return '—';
        }

        $dateLabel = self::date($date);
        $timeLabel = $date->format('H:i');

        return self::toPersianDigits($dateLabel . ' — ساعت ' . $timeLabel);
    }

    public static function today(): string
    {
        return self::date(Carbon::now());
    }

    public static function nowLabel(): string
    {
        return self::dateTime(Carbon::now());
    }

    public static function short(?Carbon $date): string
    {
        if (! $date) {
            return '—';
        }

        return self::toPersianDigits(self::date($date));
    }
}