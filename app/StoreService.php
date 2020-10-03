<?php

declare(strict_types=1);

namespace app;

class StoreService
{
    private array $refSession;
    private array $refCookie;

    public function __construct()
    {
        $this->refSession = &$_SESSION;
        $this->refCookie = &$_COOKIE;
    }

    public function getSession():?array
    {
        return $this->refSession;
    }

    public function getCookie():?array
    {
        return $this->refCookie;
    }

    public function unsetSessionValue(string $key)
    {
        unset($this->refSession[$key]);
    }

    public function unsetCookieValue(string $key)
    {
        unset($this->refCookie[$key]);
    }

    public function setSession(string $key, $value)
    {
        $this->refSession[$key]=$value;
    }

    public function setCookie(string $key, $value)
    {
        $this->refCookie[$key]=$value;
    }

    public function getSessionValue(string $key)
    {
        return $this->refSession[$key]??null;
    }

    public function getCookieValue(string $key):?string
    {
        return $this->refCookie[$key]??null;
    }
}
