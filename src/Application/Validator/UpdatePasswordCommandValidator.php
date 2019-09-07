<?php

namespace Tailgate\Application\Validator;

use Respect\Validation\Validator as V;
use Tailgate\Application\Validator\AbstractValidator;

class UpdatePasswordCommandValidator extends AbstractRespectValidator
{
    public function __construct()
    {
        parent::__construct();
    }

    protected function messageOverWrites() : array
    {
        return [
            'Password' => 'Password confirmation does not match.',
        ];
    }

    protected function addRules($command)
    {
        $this->rules['password'] = V::notEmpty()->stringType()->length(6, 100)->equals($command->getConfirmPassword())->setName('Password');
    }
}
