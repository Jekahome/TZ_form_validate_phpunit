<?php

namespace app\view;

use app\language\language;
use app\Validator;

class user
{
    public array $user;
    private string $library_errors;
    private array  $library_fields;

    public function __construct(array $user, string $library_errors, array $library_fields)
    {
        $this->user = $user;
        $this->library_errors = $library_errors;
        $this->library_fields = $library_fields;
    }

    public function index()
    {
        $library_errors = $this->library_errors;
        $library_fields = $this->library_fields;

        $patterns =  Validator::getPatterns();

        $library_user = language::init()->getLibrary('user');
        $url = WEB;
        $page = 'user';
        $user =  $this->user;
        $uri = \app\User::getInstance()->getUri();
        require_once(DIR_VIEW . 'layout.php');
    }
}
