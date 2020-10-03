<?php
declare(strict_types=1);

namespace app;

interface AbstractForm
{
    public function validate():array;
    public function isValidate():bool;
}
