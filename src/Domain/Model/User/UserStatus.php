<?php

namespace Tailgate\Domain\Model\User;

use InvalidArgumentException;

class UserStatus
{
    public const ACTIVE = 'Active';
    public const PENDING = 'Pending';
    public const DELETED = 'Deleted';

    private $value;

    private function __construct(string $value)
    {
        // check if a key was passed in
        if (in_array($value, $this->getValidKeys())) {
            $this->value = $this->getUserStatuses()[$value]['title'];

            return;
        }

        // check if a title was passed in
        if (in_array($value, $this->getValidTitles())) {
            $this->value = $value;

            return;
        }

        throw new InvalidArgumentException("Invalid user status. Status does not exist.");
    }

    public static function fromString(string $value): UserStatus
    {
        return new UserStatus($value);
    }

    public function __toString(): string
    {
        return (string) $this->value;
    }

    public function equals(UserStatus $other): bool
    {
        return $other instanceof UserStatus && $this->value === $other->value;
    }

    public static function getValidKeys()
    {
        return array_column(self::getUserStatuses(), 'key');
    }

    public static function getValidTitles()
    {
        return array_column(self::getUserStatuses(), 'title');
    }

    public static function getActive(): UserStatus
    {
        return new UserStatus(self::ACTIVE);
    }

    public static function getPending(): UserStatus
    {
        return new UserStatus(self::PENDING);
    }

    public static function getDeleted(): UserStatus
    {
        return new UserStatus(self::DELETED);
    }

    public static function getUserStatuses()
    {
        return [
            self::ACTIVE => [
                'key' => self::ACTIVE,
                'title' => "Active",
                'description' => "can use the app",
            ],
            self::PENDING => [
                'key' => self::PENDING,
                'title' => "Pending",
                'description' => "user who registered but needs to confirm email",
            ],
            self::DELETED => [
                'key' => self::DELETED,
                'title' => "Deleted",
                'description' => "user who is deleted",
            ],
        ];
    }
}
