<?php

namespace Tailgate\Domain\Model\Group;

use InvalidArgumentException;

class GroupRole
{
    public const GROUP_ADMIN = 'Group-Admin';
    public const GROUP_MEMBER = 'Group-Member';

    private $value;

    private function __construct(string $value)
    {
        // check if a key was passed in
        if (in_array($value, $this->getValidKeys())) {
            $this->value = $this->getGroupRoles()[$value]['title'];

            return;
        }

        // check if a title was passed in
        if (in_array($value, $this->getValidTitles())) {
            $this->value = $value;

            return;
        }

        throw new InvalidArgumentException("Invalid group role. Group role does not exist.");
    }

    public static function fromString(string $value): GroupRole
    {
        return new GroupRole($value);
    }

    public function __toString(): string
    {
        return (string) $this->value;
    }

    public function equals(GroupRole $other): bool
    {
        return $other instanceof GroupRole && $this->value === $other->value;
    }

    public static function getValidKeys()
    {
        return array_column(self::getGroupRoles(), 'key');
    }

    public static function getValidTitles()
    {
        return array_column(self::getGroupRoles(), 'title');
    }

    public static function getGroupAdmin(): GroupRole
    {
        return new GroupRole(self::GROUP_ADMIN);
    }

    public static function getGroupMember(): GroupRole
    {
        return new GroupRole(self::GROUP_MEMBER);
    }

    public static function getGroupRoles()
    {
        return [
            self::GROUP_ADMIN => [
                'key' => self::GROUP_ADMIN,
                'title' => "Group-Admin",
                'description' => "Someone who can manage the gorup",
            ],
            self::GROUP_MEMBER => [
                'key' => self::GROUP_MEMBER,
                'title' => "Group-Member",
                'description' => "Regular user who can submit scores",
            ],
        ];
    }
}
