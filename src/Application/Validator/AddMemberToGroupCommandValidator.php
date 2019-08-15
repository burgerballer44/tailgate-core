<?php

namespace Tailgate\Application\Validator;

use Respect\Validation\Validator as V;

class AddMemberToGroupCommandValidator extends AbstractValidator
{
    public function __construct()
    {
        parent::__construct();
    }

    protected function messageOverWrites() : array
    {
        return [
        ];
    }

    protected function addRules($command)
    {
    }
}
