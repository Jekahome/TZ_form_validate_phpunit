<?php
declare(strict_types=1);

namespace app;

class BuildFilter implements IBuildFilter
{
    private string $name;
    private string $error;
    private string $success;
    private array $callback;

    public function __construct(string $name, string $error = "", string $success = "")
    {
        $this->name = $name;
        $this->error = $error;
        $this->success = $success;
        $this->callback = [];
    }

    public function addwork(callable $userCallback)
    {
        array_push($this->callback, function (AbstractValidator $worker) use ($userCallback):ReturnValue {
            if ($userCallback($worker)) {
                return new ReturnValue(true, $this->success);
            }
            return new ReturnValue(false, $this->error);
        });
    }

    public function getName():string
    {
        return $this->name;
    }
    public function getCallback():array
    {
        return $this->callback;
    }
}

/*
class BuildFilter implements \IteratorAggregate
{
    private string $name;
    private string $error;
    private string $success;
    private array $callback;

    public function __construct(string $name, string $error = "", string $success = "")
    {
        $this->name = $name;
        $this->error = $error;
        $this->success = $success;
        $this->callback = [];
    }

    public function addWork(callable $user_callback)
    {
        array_push($this->callback, function (AbstractValidator $worker) use ($user_callback):ReturnValue {
            if ($user_callback($worker)) {
                return new ReturnValue(true, $this->success);
            } else {
                return new ReturnValue(false, $this->error);
            }
        });
    }

    public function getIterator()
    {
        $callback = $this->callback;
        array_walk($callback, function (&$item1, $key, $name) {
            $item1 = [$name=>$item1];
        }, $this->name);
        return new \ArrayIterator($callback);
    }
}
*/
