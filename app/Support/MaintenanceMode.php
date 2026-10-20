<?php

namespace App\Support;

class MaintenanceMode
{
    private static function path(): string
    {
        return storage_path('app/maintenance.json');
    }

    private static function defaults(): array
    {
        return [
            'enabled' => false,
            'message' => 'ما داریم موتور رندیکس را کوک می‌کنیم؛ چند دقیقه دیگر با سرعت، دقت و ظاهر بهتر برمی‌گردیم.',
            'telegram' => 'https://t.me/RendixDMServices',
            'updated_at' => null,
        ];
    }

    public static function data(): array
    {
        $path = self::path();

        if (! is_file($path)) {
            return self::defaults();
        }

        $json = @file_get_contents($path);
        $decoded = json_decode((string) $json, true);

        if (! is_array($decoded)) {
            return self::defaults();
        }

        return array_merge(self::defaults(), $decoded);
    }

    public static function enabled(): bool
    {
        return (bool) (self::data()['enabled'] ?? false);
    }

    public static function toggle(bool $enabled, ?string $message = null, ?string $telegram = null): array
    {
        $data = self::data();

        $data['enabled'] = $enabled;

        if ($message !== null) {
            $data['message'] = trim($message);
        }

        if ($telegram !== null) {
            $data['telegram'] = trim($telegram);
        }

        $data['updated_at'] = now()->toIso8601String();

        self::put($data);

        return $data;
    }

    private static function put(array $data): void
    {
        $path = self::path();
        $directory = dirname($path);

        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        file_put_contents(
            $path,
            json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT),
            LOCK_EX
        );
    }
}