<?php
declare(strict_types=1);

namespace app;

interface IBuildFilter
{
    public function addWork(callable $user_callback);
    public function getName():string;
    public function getCallback():array;
}
