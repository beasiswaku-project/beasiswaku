<?php

namespace App\Enums;

enum StatusEsai: string
{
    case Draft = 'draft';
    case Dikirim = 'dikirim';
    case Dinilai = 'dinilai';

    /**
     * Human readable label for the essay status.
     */
    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Draf',
            self::Dikirim => 'Dikirim',
            self::Dinilai => 'Sudah dinilai',
        };
    }
}
