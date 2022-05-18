<?php

namespace Tailgate\Domain\Model\Group;

class GroupInviteCode
{
    public const LENGTH_STRING = 10;

    private $value;

    private function __construct()
    {
        $string = substr(str_shuffle("23456789ABCDEFGHJKLMNPQRSTUVWXYZ"), 0, self::LENGTH_STRING);
        $this->value = $string . '_' . time();
    }

    public static function create(): GroupInviteCode
    {
        return new GroupInviteCode();
    }

    public function __toString(): string
    {
        return (string) $this->value;
    }
}
