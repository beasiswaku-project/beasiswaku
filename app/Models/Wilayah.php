<?php

namespace App\Models;

use Database\Factories\WilayahFactory;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Minimal model; the wilayah master module (PBI-03) builds on top of this class.
 */
#[Table('wilayah', timestamps: false)]
class Wilayah extends Model
{
    /** @use HasFactory<WilayahFactory> */
    use HasFactory;
}
