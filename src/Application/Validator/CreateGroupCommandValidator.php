<?php

namespace Tailgate\Application\Validator;

use Respect\Validation\Validator as V;
use Tailgate\Application\Validator\AbstractValidator;

class CreateGroupCommandValidator extends AbstractValidator
{
    public function __construct()
    {
        parent::__construct();
    }

    public function messageOverWrites() : array
    {
        return [
        ];
    }

    public function addRules($command)
    {
        $this->rules['name'] = V::notEmpty()->alnum()->noWhitespace()->length(4, 30)->setName('Name');
        $this->rules['owner_id'] = V::notEmpty()->stringType()->setName('Owner');
    }
}