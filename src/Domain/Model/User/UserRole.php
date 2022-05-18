<?php

namespace Tailgate\Domain\Model\User;

use InvalidArgumentException;

class UserRole
{
    public const STANDARD = 'Standard';
    public const ADMIN = 'Admin';

    private $value;

    private function __construct(string $value)
    {
        // check if a key was passed in
        if (in_array($value, $this->getValidKeys())) {
            $this->value = $this->getUserRoles()[$value]['title'];

            return;
        }

        // check if a title was passed in
        if (in_array($value, $this->getValidTitles())) {
            $this->value = $value;

            return;
        }

        throw new InvalidArgumentException("Invalid user role. Role does not exist.");
    }

    public static function fromString(string $value): UserRole
    {
        return new UserRole($value);
    }

    public function __toString(): string
    {
        return (string) $this->value;
    }

    public function equals(UserRole $other): bool
    {
        return $other instanceof UserRole && $this->value === $other->value;
    }

    public static function getValidKeys()
    {
        return array_column(self::getUserRoles(), 'key');
    }

    public static function getValidTitles()
    {
        return array_column(self::getUserRoles(), 'title');
    }

    public static function getStandard(): UserRole
    {
        return new UserRole(self::STANDARD);
    }

    public static function getAdmin(): UserRole
    {
        return new UserRole(self::ADMIN);
    }

    public static function getUserRoles()
    {
        return [
            self::STANDARD => [
                'key' => self::STANDARD,
                'title' => "Standard",
                'description' => "the average user, normal people who sign up",
            ],
            self::ADMIN => [
                'key' => self::ADMIN,
                'title' => "Admin",
                'description' => "an important person who can do whatever",
            ],
        ];
    }
}
