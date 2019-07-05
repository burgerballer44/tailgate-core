<?php

namespace Tailgate\Application\Validator;


abstract class AbstractValidator
{
    protected $rules = [];
    protected $messages = [];
    protected $errors = [];

    public function __construct()
    {
        $this->initMessages();
    }

    abstract public function initMessages();
    abstract public function assert($command);

    public function errors()
    {
        return $this->errors;
    }
}