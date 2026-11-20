<?php

namespace App\Enums;

enum JenisPrestasi: string
{
    case Akademik = 'akademik';
    case NonAkademik = 'non_akademik';
    case Organisasi = 'organisasi';

    /**
     * Human readable label for the achievement type.
     */
    public function label(): string
    {
        return match ($this) {
            self::Akademik => 'Prestasi akademik',
            self::NonAkademik => 'Prestasi nonakademik',
            self::Organisasi => 'Organisasi',
        };
    }
}
