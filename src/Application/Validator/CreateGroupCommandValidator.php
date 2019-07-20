<?php

namespace Tailgate\Application\Validator;

use Respect\Validation\Validator as V;
use Tailgate\Domain\Model\User\UserViewRepositoryInterface;

class CreateGroupCommandValidator extends AbstractValidator
{
    public function __construct(UserViewRepositoryInterface $userViewRepository)
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
        $this->rules['name'] = V::notEmpty()->alnum()->noWhitespace()->length(4, 30)->setName('Name');
        $this->rules['owner_id'] = V::notEmpty()->stringType()->setName('Owner');
    }
}