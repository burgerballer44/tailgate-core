<?php

namespace Tailgate\Application\Validator\User;

use Respect\Validation\Exceptions\ValidationException;

class UniqueUsernameException extends ValidationException
{
    public static $defaultTemplates = [
        self::MODE_DEFAULT => [
            self::STANDARD => '{{name}} is taken. Please choose a unique username.',
        ],
        self::MODE_NEGATIVE => [
            self::STANDARD => '{{name}} is taken. Please choose a... not unique username.',
        ]
    ];
}