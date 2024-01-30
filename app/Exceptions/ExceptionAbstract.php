<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Support\MessageBag;

abstract class ExceptionAbstract extends Exception
{
    /**
     * @var MessageBag
     */
    protected MessageBag $errors;

    /**
     * @param MessageBag|string|array<mixed> $errors
     * @param int $code
     * @param Exception|null $previous
     */
    public function __construct(MessageBag|string|array $errors, int $code = 0, Exception $previous = null)
    {
        $this->setError($errors);
        parent::__construct($this->errors->first(), $code, $previous);
    }

    /**
     * @return MessageBag
     */
    public function getErrors(): MessageBag
    {
        return $this->errors;
    }

    /**
     * @param MessageBag|string|array<mixed> $errors
     * @return void
     */
    protected function setError(MessageBag|string|array $errors): void
    {
        if (is_string($errors)) {
            $errors = ['error' => $errors];
        }

        if (is_array($errors)) {
            $errors = new MessageBag($errors);
        }

        $this->errors = $errors;
    }
}
