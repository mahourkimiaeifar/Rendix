<?php

namespace App\Support;

use Carbon\Carbon;
use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use Morilog\Jalali\Jalalian;
use Throwable;

class Jalali
{
    /**
     * آفست ثابت تهران.
     * عمداً از Asia/Tehran استفاده نمی‌کنیم تا باگ tzdata/سیستم اذیت نکند.
     */
    public const TEHRAN_OFFSET = '+03:30';

    private static function zone(): DateTimeZone
    {
        return new DateTimeZone(self::TEHRAN_OFFSET);
    }

    private static function persianMap(): array
    {
        return [
            '0' => "\u{06F0}",
            '1' => "\u{06F1}",
            '2' => "\u{06F2}",
            '3' => "\u{06F3}",
            '4' => "\u{06F4}",
            '5' => "\u{06F5}",
            '6' => "\u{06F6}",
            '7' => "\u{06F7}",
            '8' => "\u{06F8}",
            '9' => "\u{06F9}",
        ];
    }

    private static function arabicMap(): array
    {
        return [
            "\u{0660}" => '0',
            "\u{0661}" => '1',
            "\u{0662}" => '2',
            "\u{0663}" => '3',
            "\u{0664}" => '4',
            "\u{0665}" => '5',
            "\u{0666}" => '6',
            "\u{0667}" => '7',
            "\u{0668}" => '8',
            "\u{0669}" => '9',
        ];
    }

    private static function latinMap(): array
    {
        $map = [];

        foreach (self::persianMap() as $latin => $persianDigit) {
            $map[$persianDigit] = $latin;
        }

        foreach (self::arabicMap() as $arabicDigit => $latin) {
            $map[$arabicDigit] = $latin;
        }

        return $map;
    }

    public static function toLatinDigits(?string $value): string
    {
        return strtr((string) $value, self::latinMap());
    }

    public static function toPersianDigits(string $value): string
    {
        return strtr($value, self::persianMap());
    }

    public static function now(): Carbon
    {
        return Carbon::instance(new DateTimeImmutable('now', self::zone()));
    }

    private static function normalize(mixed $value): ?Carbon
    {
        try {
            if ($value instanceof Carbon) {
                $carbon = $value->copy();
            } elseif ($value instanceof DateTimeInterface) {
                $carbon = Carbon::instance($value);
            } elseif (is_string($value)) {
                $value = trim($value);

                if ($value === '') {
                    return null;
                }

                // اگر رشته تاریخ/ساعت ساده بود، فرض می‌کنیم ساعت تهران است.
                if (preg_match('/^\d{4}-\d{2}-\d{2}[ T]\d{2}:\d{2}:\d{2}$/', $value)) {
                    $value = str_replace('T', ' ', $value);

                    return Carbon::createFromFormat(
                        'Y-m-d H:i:s',
                        $value,
                        self::zone()
                    );
                }

                $carbon = Carbon::parse($value);
            } elseif (is_int($value)) {
                $carbon = Carbon::createFromTimestamp($value, 'UTC');
            } else {
                return null;
            }

            return $carbon
                ->setTimezone('UTC')
                ->setTimezone(self::zone());
        } catch (Throwable) {
            return null;
        }
    }

    private static function jalaliDateString(Carbon $date): string
    {
        try {
            $jalalian = Jalalian::fromCarbon($date);

            $year = (int) $jalalian->getYear();
            $month = (int) $jalalian->getMonth();
            $day = (int) $jalalian->getDay();

            if (
                $year >= 1000 &&
                $year <= 9999 &&
                $month >= 1 &&
                $month <= 12 &&
                $day >= 1 &&
                $day <= 31
            ) {
                return sprintf('%04d/%02d/%02d', $year, $month, $day);
            }
        } catch (Throwable) {
            // fallback
        }

        return $date->format('Y-m-d');
    }

    public static function parse(?string $value): ?Carbon
    {
        $value = trim(self::toLatinDigits((string) $value));

        if ($value === '') {
            return null;
        }

        $value = preg_replace('/\s+/u', ' ', $value);

        $time = null;

        if (preg_match('/(\d{1,2}):(\d{2})(?::\d{2})?$/u', $value, $matches)) {
            $time = sprintf('%02d:%02d', (int) $matches[1], (int) $matches[2]);
            $value = trim(preg_replace('/\s*\d{1,2}:\d{2}(?::\d{2})?$/u', '', $value));
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
            $base = $jalalian->toCarbon();

            $carbon = Carbon::createFromFormat(
                'Y-m-d H:i:s',
                $base->format('Y-m-d H:i:s'),
                self::zone()
            );

            if ($time) {
                [$hour, $minute] = explode(':', $time);
                $carbon->setTime((int) $hour, (int) $minute, 0);
            }

            return $carbon;
        } catch (Throwable) {
            return null;
        }
    }

    public static function date(mixed $date): string
    {
        $carbon = self::normalize($date);

        if (! $carbon) {
            return '';
        }

        return self::jalaliDateString($carbon);
    }

    public static function dateTime(mixed $date): string
    {
        $carbon = self::normalize($date);

        if (! $carbon) {
            return '—';
        }

        $jalali = self::jalaliDateString($carbon);
        $time = (string) $carbon->format('H:i');

        return self::toPersianDigits($jalali . ' — ساعت ' . $time);
    }

    public static function today(): string
    {
        return self::date(self::now());
    }

    public static function nowLabel(): string
    {
        return self::dateTime(self::now());
    }

    public static function short(mixed $date): string
    {
        $carbon = self::normalize($date);

        if (! $carbon) {
            return '—';
        }

        return self::toPersianDigits(self::date($carbon));
    }
}