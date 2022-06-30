<?php

namespace App\Exceptions;

use Exception;
use Throwable;

class ValidateException extends Exception
{
    private $data;

    /**
     * ValidateException constructor.
     * @param string $message
     * @param array $data
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct(string $message = "数据校验失败", $data = [], int $code = 0, Throwable $previous = null)
    {
        $this->data = $data;
        parent::__construct($message, $code, $previous);
    }

    public function getData()
    {
        return $this->data;
    }
}
