<?php

declare(strict_types=1);

namespace app;

use app\AbstractValidator;
use app\AbstractForm;
use app\BuildFilter;
use app\ManagerValidator;
use app\filters\FilterAnd;
use app\filters\FilterOr;
use app\language\language;

// Что бы небыло дублирования кода с одинаковыми правилами валидации, вынес содание их в класс Validator.
// При валидации связываются доступные свойства класса с свойствами валидируемого класса через метод activateField
// Возможно использование методов Closure выразилось в более гибкий код.
class Validator implements IValidator
{
    private ManagerValidator $managerValidator;
    private array $calbacks;
    private array $fields;
    private array $patterns;

    public function __construct()
    {
        $this->managerValidator = new ManagerValidator();
        $this->calbacks = [];
        $this->fields = ['name'=>null,'login'=>null,'email'=>null,'password'=>null,'confirmPassword'=>null,'file'=>null];
        $this->patterns = Validator::getPatterns();
        $this->buildValidation();
    }

    public function __set(string $name, $value)
    {
        if (array_key_exists($name, $this->fields)) {
            $this->fields[$name] = $value;
            $this->managerValidator->add($this->calbacks[$name]);
        } else {
            throw new \RuntimeException("Field not found");
        }
    }

    public function __get(string $name)
    {
        return $this->fields[$name];
    }

    /**
     * TODO После активации поля отработает _set
     */
    public function activateField(string $field, $value)
    {
        if (array_key_exists($field, $this->fields)) {
            $this->$field = $value;
        } else {
            throw new \RuntimeException("Field not found");
        }
    }

    /**
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function buildValidation()
    {
        $messageField = language::init()->getLibrary('message_field');

        $buildName = new BuildFilter("name", $messageField['name']);
        $buildName->addWork(function () {
            if (filter_var(
                $this->name,
                FILTER_VALIDATE_REGEXP,
                ["options" => ["regexp" => "/".$this->patterns['name']."/"]]
            ) === false) {
                return false;
            }
            return true;
        });
        $filteName= new FilterAnd();
        $filteName->addFilter($buildName);
        $this->calbacks['name']=$filteName;

        $buildLogin1 = new BuildFilter("login", $messageField['login']);
        $buildLogin1->addWork(function () {
            if (filter_var(
                $this->login,
                FILTER_VALIDATE_REGEXP,
                ["options" => ["regexp" => "/".$this->patterns['login']."/"]]
            ) === false) {
                return false;
            }
            return true;
        });

        $buildLogin2 = new BuildFilter("login", $messageField['login2']);
        $buildLogin2->addWork(function () {
            if (mb_strlen($this->login) < 4) {
                return false;
            }
            return true;
        });
        $buildLogin3 = new BuildFilter("login", $messageField['login3']);
        $buildLogin3->addWork(function () {
            if (mb_strlen($this->login) > 9) {
                return false;
            }
            return true;
        });

        $filterLogin = new FilterAnd();
        $filterLogin->addFilter($buildLogin1);
        $filterLogin->addFilter($buildLogin2);
        $filterLogin->addFilter($buildLogin3);
        $this->calbacks['login']=$filterLogin;


        $buildEmail = new BuildFilter("email", $messageField['email']);
        $buildEmail->addWork(function () {
            if (filter_var($this->email, FILTER_VALIDATE_EMAIL) === false) {
                return false;
            }
            return true;
        });
        $buildEmail->addWork(function () {
            if (filter_var(
                $this->email,
                FILTER_VALIDATE_REGEXP,
                ["options" => ["regexp" => "/^(?!.*ru)[a-zA-Z].+$/"]]
            ) === false) {
                return false;
            }
            return true;
        });

        $filterEmail = new FilterAnd();
        $filterEmail->addFilter($buildEmail);
        $this->calbacks['email']=$filterEmail;


        $buildPassword1 = new BuildFilter("password", $messageField['password']);
        $buildPassword1->addWork(function () {
            if (filter_var(
                $this->password,
                FILTER_VALIDATE_REGEXP,
                ["options" => ["regexp" =>"/".$this->patterns['password']."/"]]
            ) === false) {
                return false;
            }
            return true;
        });

        $buildPassword2 = new BuildFilter("password", $messageField['password2']);
        $buildPassword2->addWork(function () {
            if (mb_strlen($this->password) < 4) {
                return false;
            }
            return true;
        });
        $buildPassword3 = new BuildFilter("password", $messageField['password3']);
        $buildPassword3->addWork(function () {
            if (mb_strlen($this->password) > 9) {
                return false;
            }
            return true;
        });

        $filterPassword = new FilterAnd();
        $filterPassword->addFilter($buildPassword1);
        $filterPassword->addFilter($buildPassword2);
        $filterPassword->addFilter($buildPassword3);
        $this->calbacks['password']=$filterPassword;


        $buildConfirmPass = new BuildFilter("confirmPassword", $messageField['confirmPassword']);
        $buildConfirmPass->addWork(function (AbstractValidator $worker) use ($filterPassword) {
            // !$worker->currentValidate() || !$filterPassword->isValidate()
            if ($this->confirmPassword !== $this->password) {
                return false;
            }
            return true;
        });
        $filterConfirmPass = new FilterAnd();
        $filterConfirmPass->addFilter($buildConfirmPass);
        $this->calbacks['confirmPassword']=$filterConfirmPass;


        $buildFile = new BuildFilter("file", $messageField['file']);
        $buildFile->addWork(function () {
            if (!empty($this->file['file'])) {
                if ($this->file['file']['size'] > 200000) {
                    return false;
                }
            }
            return true;
        });

        $buildFile2 = new BuildFilter("file", $messageField['file2']);
        $buildFile2->addWork(function () {
            if (!isset($this->file['files']['error']) ||
                is_array($this->file['files']['error'])
            ) {
                return false;
            }

            if ($this->file['files']['error'] != UPLOAD_ERR_OK) {
                return false;
            }
            return true;
        });

        $buildFile3 = new BuildFilter("file", $messageField['file3']);
        $buildFile3->addWork(function () {
            $finfo = new \finfo(FILEINFO_MIME_TYPE);

            if (false ===  array_search(
                $finfo->file($this->file['files']['tmp_name']),
                $this->patterns['file'],
                true
            )) {
                return false;
            }
            return true;
        });

        $filterFile = new FilterAnd();
        $filterFile->addFilter($buildFile);
        $filterFile->addFilter($buildFile2);
        $this->calbacks['file']=$filterFile;
    }

    public function validate():array
    {
        return $this->managerValidator->validate();
    }

    public function isValidate():bool
    {
        return $this->managerValidator->isValidate();
    }
    public static function getPatterns():array
    {
        return require FILE_PATTERNS_FIELD;
    }
}
