<?php

namespace Tailgate\Application\Validator;

use Respect\Validation\Validator as V;
use Tailgate\Application\Validator\AbstractValidator;
use Tailgate\Application\Validator\User\UniqueEmail;
use Tailgate\Application\Validator\User\UniqueUsername;
use Tailgate\Domain\Model\User\UserViewRepositoryInterface;

class SignUpUserCommandValidator extends AbstractValidator
{
    private $userViewRepository;

    public function __construct(UserViewRepositoryInterface $userViewRepository)
    {
        $this->userViewRepository = $userViewRepository;
        parent::__construct();
    }

    public function initMessages()
    {
        $this->messages = [
            'notEmpty'       => '{{name}} is required.',
            'alnum'          => '{{name}} must only contain alphanumeric characters.',
            'noWhitespace'   => '{{name}} must not contain white spaces.',
            'length'         => '{{name}} must be between {{minValue}} and {{maxValue}} characters.',
            'Password'       => 'Password confirmation does not match.',
            'email'          => 'Please make sure you typed a correct email address.',
            'uniqueUsername' => 'This username is unavailable. Please choose a unique username.',
            'uniqueEmail'    => 'This email is unavailable. Please choose a unique email.',
        ];
    }

    public function assert($command)
    {

        V::with("Tailgate\Application\Validator\User\\");

        $this->rules['username'] = V::notEmpty()->alnum()->noWhitespace()->length(4, 20)->UniqueUsername($this->userViewRepository)->setName('Username');
        $this->rules['password'] = V::notEmpty()->stringType()->length(6, 100)->equals($command->getConfirmPassword())->setName('Password');
        $this->rules['email'] = V::notEmpty()->email()->UniqueEmail($this->userViewRepository)->setName('Email');

        foreach ($this->rules as $field => $validator) {
            $method = 'get' . ucfirst($field);

            try {
                $validator->assert($command->$method());
            } catch (\Throwable $ex) {
                // $this->errors[$field] = $ex->getMessages();
                // $this->errors[$field] = $ex->findMessages($this->messages);
                $this->errors[$field] = array_filter($ex->findMessages($this->messages), function($error) {
                    return !empty($error);
                });
                // $this->errors[$field] = $ex->getFullMessage();
            }
        }

        if (!empty($this->errors)) {
            return false;
        } 

        return true;
    }
}