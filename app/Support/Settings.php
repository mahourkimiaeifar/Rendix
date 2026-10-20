<?php

namespace App\Support;

use App\Models\Setting;
use Illuminate\Support\Facades\Schema;
use Throwable;

class Settings
{
    private static ?array $cached = null;

    public static function all(): array
    {
        if (self::$cached !== null) {
            return self::$cached;
        }

        try {
            if (! Schema::hasTable('settings')) {
                return self::$cached = [];
            }

            $settings = Setting::query()->get();

            return self::$cached = $settings
                ->mapWithKeys(fn (Setting $setting) => [
                    $setting->key => $setting->value,
                ])
                ->all();
        } catch (Throwable $e) {
            return self::$cached = [];
        }
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        $all = self::all();

        // اول کلید مستقیم، مثلاً "site.name"
        if (array_key_exists($key, $all)) {
            return $all[$key];
        }

        // بعد اگر خواستی ساختار تودرتو هم پشتیبانی شود
        return data_get($all, $key, $default);
    }

    public static function set(string $key, mixed $value): void
    {
        Setting::query()->updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );

        self::$cached = null;
    }

    public static function setMany(array $values): void
    {
        foreach ($values as $key => $value) {
            Setting::query()->updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        self::$cached = null;
    }

    public static function forget(string $key): void
    {
        Setting::query()->where('key', $key)->delete();

        self::$cached = null;
    }
}