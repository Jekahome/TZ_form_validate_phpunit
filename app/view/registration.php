<?php

namespace app\view;

use app\language\language;
use app\Validator;

class registration
{
    private array $fields;
    private string $library_errors;
    private array  $library_fields;

    public function __construct(array $fields, string $library_errors, array $library_fields)
    {
        $this->fields = $fields;
        $this->library_errors = $library_errors;
        $this->library_fields = $library_fields;
    }

    public function index()
    {
        $library_errors = $this->library_errors;
        $library_fields = $this->library_fields;
        $fields = $this->fields;
        $patterns = Validator::getPatterns();
        $url = WEB;
        $page = 'registration';

        require_once(DIR_VIEW . 'layout.php');
    }
}
