<?php

namespace App\Enums;

use Illuminate\Support\Number;

enum JenisPersyaratan: string
{
    case IpkMinimum = 'ipk_minimum';
    case PenghasilanMaksimum = 'penghasilan_maksimum';
    case SemesterMinimum = 'semester_minimum';
    case Kipk = 'kipk';
    case Dtsen = 'dtsen';
    case Wilayah3t = 'wilayah_3t';
    case Dokumen = 'dokumen';

    /**
     * Human readable label for the requirement type.
     */
    public function label(): string
    {
        return match ($this) {
            self::IpkMinimum => 'IPK minimum',
            self::PenghasilanMaksimum => 'Penghasilan orang tua maksimum',
            self::SemesterMinimum => 'Semester minimum',
            self::Kipk => 'Penerima KIP Kuliah',
            self::Dtsen => 'Terdaftar DTSEN',
            self::Wilayah3t => 'Berasal dari wilayah 3T',
            self::Dokumen => 'Dokumen',
        };
    }

    /**
     * Whether the requirement needs a value (number or document name).
     * For DTSEN the value is optional: the highest accepted decile.
     */
    public function butuhNilai(): bool
    {
        return in_array($this, [self::IpkMinimum, self::PenghasilanMaksimum, self::SemesterMinimum, self::Dokumen], true);
    }

    /**
     * Document requirements are fulfilled by uploading a file when registering,
     * so they are not part of the matching percentage.
     */
    public function isDokumen(): bool
    {
        return $this === self::Dokumen;
    }

    /**
     * Describe a requirement of this type with its value, e.g. "IPK minimum 3,00".
     */
    public function deskripsi(?string $nilai): string
    {
        return match ($this) {
            self::IpkMinimum => 'IPK minimum '.number_format((float) $nilai, 2, ',', '.'),
            self::PenghasilanMaksimum => 'Penghasilan orang tua maksimum '.Number::currency((float) $nilai, in: 'IDR', locale: 'id', precision: 0).' per bulan',
            self::SemesterMinimum => "Minimal semester {$nilai}",
            self::Dtsen => 'Terdaftar DTSEN'.($nilai ? " desil 1–{$nilai}" : ''),
            self::Dokumen => "Dokumen: {$nilai}",
            default => $this->label(),
        };
    }
}
