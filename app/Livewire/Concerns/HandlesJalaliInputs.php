<?php

namespace App\Livewire\Concerns;

use App\Support\Jalali;
use Carbon\Carbon;

trait HandlesJalaliInputs
{
    public function setJalaliToday(string $property): void
    {
        $this->{$property} = Jalali::today();
        $this->syncJalaliProperty($property);
    }

    public function clearJalali(string $property): void
    {
        $this->{$property} = '';
        $this->syncJalaliProperty($property);
    }

    public function updated(string $propertyName, mixed $value): void
    {
        if (str_ends_with($propertyName, '_jalali')) {
            $this->syncJalaliProperty($propertyName);
        }
    }

    protected function syncJalaliProperty(string $propertyName): void
    {
        $baseName = substr($propertyName, 0, -strlen('_jalali'));

        if ($baseName === '' || ! property_exists($this, $baseName)) {
            return;
        }

        $this->{$baseName} = Jalali::parse($this->{$propertyName} ?? '');
    }

    protected function syncAllJalaliProperties(): void
    {
        foreach (get_object_vars($this) as $property => $value) {
            if (str_ends_with($property, '_jalali')) {
                $this->syncJalaliProperty($property);
            }
        }
    }

    protected function fillJalaliFromCarbon(string $baseName, ?Carbon $date): void
    {
        $propertyName = $baseName . '_jalali';

        if (property_exists($this, $propertyName)) {
            $this->{$propertyName} = Jalali::date($date);
        }
    }
}