<?php

namespace Tailgate\Domain\Model\User;

class PasswordResetToken
{
    public const LENGTH_STRING = 20;
    public const TIME_TO_EXPIRE = 3600; // 1 hour in seconds

    private $value;

    private function __construct()
    {
        $string = substr(str_shuffle("23456789ABCDEFGHJKLMNPQRSTUVWXYZ"), 0, self::LENGTH_STRING);
        $this->value = $string . '_' . time();
    }

    public static function create(): PasswordResetToken
    {
        return new PasswordResetToken();
    }

    public function __toString(): string
    {
        return (string) $this->value;
    }

    // determine if password reset token is valid
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        $expire = self::TIME_TO_EXPIRE;

        return $timestamp + $expire >= time();
    }
}
