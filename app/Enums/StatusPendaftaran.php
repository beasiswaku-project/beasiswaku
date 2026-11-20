<?php

namespace App\Enums;

enum StatusPendaftaran: string
{
    case Draft = 'draft';
    case Dikirim = 'dikirim';
    case Verifikasi = 'verifikasi';
    case Seleksi = 'seleksi';
    case Lolos = 'lolos';
    case TidakLolos = 'tidak_lolos';
    case Diterima = 'diterima';

    /**
     * Human readable label for the registration status.
     */
    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Draf',
            self::Dikirim => 'Dikirim',
            self::Verifikasi => 'Verifikasi berkas',
            self::Seleksi => 'Tahap seleksi',
            self::Lolos => 'Lolos',
            self::TidakLolos => 'Tidak lolos',
            self::Diterima => 'Diterima',
        };
    }

    /**
     * Tailwind classes for the status badge.
     */
    public function badgeClass(): string
    {
        return match ($this) {
            self::Draft => 'bg-gray-100 text-gray-600',
            self::Dikirim, self::Verifikasi, self::Seleksi => 'bg-blue-50 text-blue-700',
            self::Lolos, self::Diterima => 'bg-green-50 text-green-700',
            self::TidakLolos => 'bg-red-50 text-red-700',
        };
    }

    /**
     * Statuses an admin may assign; a draft is only ever set by the system.
     *
     * @return list<self>
     */
    public static function untukAdmin(): array
    {
        return array_values(array_filter(self::cases(), fn (self $status): bool => $status !== self::Draft));
    }
}
