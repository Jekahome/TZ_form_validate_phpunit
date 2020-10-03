<?php

namespace app\view;

use app\language\language;

class index
{
    public function __construct()
    {
    }

    public function index()
    {
        $library_home =  language::init()->getLibrary('index');

        $url = WEB;
        $page = 'index';

        require_once(DIR_VIEW . 'layout.php');
    }
}
