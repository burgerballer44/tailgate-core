<?php

namespace Tailgate\Application\Validator;

use Respect\Validation\Validator as V;
use Tailgate\Application\Validator\User\UniqueEmail;
use Tailgate\Application\Validator\User\UniqueUsername;
use Tailgate\Application\Validator\User\UniqueEmailException;
use Tailgate\Application\Validator\User\UniqueUsernameException;

class SignUpUserCommandValidator
{
    protected $rules = [];
    protected $messages = [];
    protected $errors = [];

    public function __construct()
    {
        $this->initRules();
        $this->initMessages();
    }
    
    public function initRules()
    {
        v::with("Tailgate\Application\Validator\User\\");

        $this->rules['username'] = V::stringType()->noWhitespace()->length(5, null)->UniqueEmail()->UniqueUsername();
        $this->rules['password'] = V::stringType()->length(5, null);
        $this->rules['email'] = V::email();
    }

    public function initMessages()
    {
        $this->messages = [];
    }

    public function assert(array $inputs)
    {
        foreach ($this->rules as $field => $validator) {
            try {
                $validator->assert(array_get($inputs, $field));
            } catch (\Respect\Validation\Exceptions\NestedValidationExceptionInterface $ex) {
                $this->errors = $ex->findMessages($this->messages);
                return false;
            }
        }

        return true;
    }

    public function errors()
    {
        return $this->errors;
    }
}