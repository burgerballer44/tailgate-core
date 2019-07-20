<?php

namespace Tailgate\Application\Validator;

use Respect\Validation\Validator as V;
use Tailgate\Domain\Model\User\UserViewRepositoryInterface;

class AddGameCommandValidator extends AbstractValidator
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
        
    }
}