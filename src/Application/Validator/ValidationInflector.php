<?php

namespace Tailgate\Application\Validator;

use Verraes\ClassFunctions\ClassFunctions;

use Tailgate\Application\Validator\User\SignUpUserCommandValidator;

class ValidationInflector
{
    public function getValidatorClass($command)
    {   
        $classname = 'Tailgate\Application\Validator\\' . ClassFunctions::short($command) . 'Validator';

        return $classname;
    }
}