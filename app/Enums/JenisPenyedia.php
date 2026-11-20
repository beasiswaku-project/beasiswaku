<?php

namespace App\Enums;

enum JenisPenyedia: string
{
    case Pemerintah = 'pemerintah';
    case Swasta = 'swasta';
    case Kampus = 'kampus';
    case Yayasan = 'yayasan';

    /**
     * Human readable label for the provider type.
     */
    public function label(): string
    {
        return match ($this) {
            self::Pemerintah => 'Pemerintah',
            self::Swasta => 'Perusahaan swasta',
            self::Kampus => 'Kampus',
            self::Yayasan => 'Yayasan',
        };
    }
}
