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

    abstract protected function messageOverWrites() : array;
    abstract protected function addRules($command);

    public function assert($command)
    {
        $this->addRules($command);
        $this->assertEachField($command);

        if (!empty($this->errors)) {
            return false;
        } 

        return true;
    }

    protected function getMethodNameFromField($field)
    {
        return 'get' . str_replace('_', '', ucwords($field, '_'));
    }

    protected function assertEachField($command)
    {
        foreach ($this->rules as $field => $validator) {
            $method = $this->getMethodNameFromField($field);

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
    }

    private function initMessages()
    {
        $this->messages = array_merge([
            'notEmpty'       => '{{name}} is required.',
            'alnum'          => '{{name}} must only contain alphanumeric characters.',
            'noWhitespace'   => '{{name}} must not contain white spaces.',
            'length'         => '{{name}} must be between {{minValue}} and {{maxValue}} characters.',
            'email'          => 'Please make sure you typed a correct email address.',
        ], $this->messageOverWrites());
    }
}