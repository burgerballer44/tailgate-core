<?php

namespace Tailgate\Application\Validator;

use Respect\Validation\Validator as V;
use Tailgate\Application\Validator\AbstractValidator;
use Tailgate\Application\Validator\User\UniqueEmail;
use Tailgate\Application\Validator\User\UniqueUsername;
use Tailgate\Domain\Model\User\UserViewRepositoryInterface;

class RegisterUserCommandValidator extends AbstractValidator
{
    private $userViewRepository;

    public function __construct(UserViewRepositoryInterface $userViewRepository)
    {
        $this->userViewRepository = $userViewRepository;
        parent::__construct();
    }

    protected function messageOverWrites() : array
    {
        return [
            'Password'       => 'Password confirmation does not match.',
            'uniqueUsername' => 'This username is unavailable. Please choose a unique username.',
            'uniqueEmail'    => 'This email is unavailable. Please choose a unique email.',
        ];
    }

    protected function addRules($command)
    {
        V::with("Tailgate\Application\Validator\User\\");

        $this->rules['username'] = V::notEmpty()->alnum()->noWhitespace()->length(4, 20)->UniqueUsername($this->userViewRepository)->setName('Username');
        $this->rules['password'] = V::notEmpty()->stringType()->length(6, 100)->equals($command->getConfirmPassword())->setName('Password');
        $this->rules['email'] = V::notEmpty()->email()->length(4, 100)->UniqueEmail($this->userViewRepository)->setName('Email');
    }
}
