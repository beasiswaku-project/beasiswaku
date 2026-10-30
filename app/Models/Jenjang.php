<?php

namespace App\Models;

use Database\Factories\JenjangFactory;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Minimal model; the jenjang master module (PBI-02) builds on top of this class.
 */
#[Table('jenjang', timestamps: false)]
class Jenjang extends Model
{
    /** @use HasFactory<JenjangFactory> */
    use HasFactory;
}
