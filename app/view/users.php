<?php

namespace app\view;

use app\language\language;

class users
{
    private array $users;
    public function __construct(array $users)
    {
        $this->users = $users;
    }

    public function index()
    {
        $library_user = language::init()->getLibrary('users');
        $url = WEB;
        $page = 'users';
        $users =  $this->users;
        require_once(DIR_VIEW . 'layout.php');
    }
}
