<?php

namespace App\Enums;

enum UserRole: string
{
    case Admin = 'admin';
    case Mahasiswa = 'mahasiswa';

    /**
     * Human readable label for the role.
     */
    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Administrator',
            self::Mahasiswa => 'Mahasiswa',
        };
    }

    /**
     * Name of the route a user with this role lands on after authenticating.
     */
    public function homeRoute(): string
    {
        return match ($this) {
            self::Admin => 'admin.dashboard',
            self::Mahasiswa => 'mahasiswa.dashboard',
        };
    }
}
