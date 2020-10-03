<?php
declare(strict_types=1);

namespace app;

use app\IBuildFilter;

abstract class AbstractValidator
{
    private static bool $isValidCurrent = true;
    abstract public function validate():array;
    abstract public function addFilter(IBuildFilter $filter);
    abstract public function isValidate():bool;

    public function reset()
    {
        static::$isValidCurrent = true;
    }

    public function currentValidate():bool
    {
        return static::$isValidCurrent;
    }

    protected function invalidate()
    {
        static::$isValidCurrent = false;
    }
}
