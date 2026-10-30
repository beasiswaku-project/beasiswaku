<?php

namespace App\Models;

use Database\Factories\PenyediaFactory;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Minimal model; the penyedia module (PBI-07) builds on top of this class.
 */
#[Table('penyedia', timestamps: false)]
class Penyedia extends Model
{
    /** @use HasFactory<PenyediaFactory> */
    use HasFactory;
}
