<?php

namespace App\Enums;

enum StatusVerifikasi: string
{
    case Menunggu = 'menunggu';
    case Valid = 'valid';
    case TidakValid = 'tidak_valid';

    /**
     * Human readable label for the verification status.
     */
    public function label(): string
    {
        return match ($this) {
            self::Menunggu => 'Menunggu verifikasi',
            self::Valid => 'Valid',
            self::TidakValid => 'Ditolak',
        };
    }

    /**
     * Tailwind classes for the status badge.
     */
    public function badgeClass(): string
    {
        return match ($this) {
            self::Menunggu => 'bg-gray-100 text-gray-600',
            self::Valid => 'bg-green-50 text-green-700',
            self::TidakValid => 'bg-red-50 text-red-700',
        };
    }
}
