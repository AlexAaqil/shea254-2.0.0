<?php

namespace App\Enums;

enum UserRoles: int
{
    case SUPER_ADMIN = 0;
    case ADMIN = 1;
    case USER = 2;

    public function label():string
    {
        return match($this) {
            self::SUPER_ADMIN => 'Super Admin',
            self::ADMIN => 'Admin',
            self::USER => 'User',
        };
    }

    public static function labels():array
    {
        $labels = [];

        foreach(self::cases() as $role) {
            $labels[$role->value] = $role->label();
        }

        return $labels;
    }

    public static function adminLabels(): array
    {
        return [
            self::USER->value => self::USER->label(),
            self::ADMIN->value => self::ADMIN->label(),
        ];
    }

    public static function superAdminLabels(): array
    {
        return [
            self::SUPER_ADMIN->value => self::SUPER_ADMIN->label(),
            self::ADMIN->value => self::ADMIN->label(),
            self::USER->value => self::USER->label(),
        ];
    }
}
