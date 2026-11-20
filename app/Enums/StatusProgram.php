<?php

namespace App\Enums;

enum StatusProgram: string
{
    case Draft = 'draft';
    case Dibuka = 'dibuka';
    case Ditutup = 'ditutup';
    case Selesai = 'selesai';

    /**
     * Human readable label for the program status.
     */
    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Draf',
            self::Dibuka => 'Dibuka',
            self::Ditutup => 'Ditutup',
            self::Selesai => 'Selesai',
        };
    }

    /**
     * Tailwind classes for the status badge.
     */
    public function badgeClass(): string
    {
        return match ($this) {
            self::Draft => 'bg-gray-100 text-gray-600',
            self::Dibuka => 'bg-green-50 text-green-700',
            self::Ditutup => 'bg-amber-50 text-amber-700',
            self::Selesai => 'bg-blue-50 text-blue-700',
        };
    }
}
