<?php
declare(strict_types=1);

namespace app;

class ReturnValue
{
    private bool $isValidate = true;
    private string $message;
    public function __construct(bool $isValidate, string $message = "")
    {
        $this->isValidate = $isValidate;
        $this->message = $message;
    }
    public function getMessage():string
    {
        return  $this->message;
    }
    public function isValidate():bool
    {
        return  $this->isValidate;
    }
}
