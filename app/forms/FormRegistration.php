<?php
declare(strict_types=1);

namespace app\forms;

use app\AbstractValidator;
use app\AbstractForm;
use app\ManagerValidator;
use app\BuildFilter;
use app\filters\FilterAnd;
use app\filters\FilterOr;
use app\IValidator;
use app\Validator;
use app\language\language;

class FormRegistration implements AbstractForm
{
    public string $name;
    public string $login;
    public string $email;
    public string $password;
    public string $confirmPassword;
    public array $file;

    private IValidator $validator;

    public function __construct(IValidator $validator = null)
    {
        if (!is_null($validator)) {
            $this->validator = $validator;
        }
    }

    public function post(array $input, array $file = null):array
    {
        try {
            parse_str($input['fields'], $fields);

            [
                'name' => $this->name,
                'email' => $this->email,
                'login' => $this->login,
                'password' => $this->password,
                'confirmPassword' => $this->confirmPassword
            ] = $fields;

            $this->file = $file;

            $this->validator->activateField('name', $this->name);
            $this->validator->activateField('login', $this->login);
            $this->validator->activateField('email', $this->email);
            $this->validator->activateField('password', $this->password);
            $this->validator->activateField('confirmPassword', $this->confirmPassword);
            $this->validator->activateField('file', $this->file);

            $errors = $this->validate();
            return $errors;
            /*if (!$this->validator->isValidate()) {
                // render with errors
                echo __CLASS__." - Validation error\n\n";
                return $errors;
            }
            echo __CLASS__." - Validation success\n\n";
            return [];*/
        } catch (\Throwable $e) {
            // user data error
            return [];
        }
    }

    public function validate():array
    {
        return $this->validator->validate();
    }

    public function isValidate():bool
    {
        return $this->validator->isValidate();
    }

    public function getFields():array
    {
        return ['name' => $this->name??'', 'email' => $this->email??'', 'login' => $this->login??'', 'password' => $this->password??'', 'confirmPassword' => $this->confirmPassword??''];
    }

    public function getFieldErrors():string
    {
        return json_encode(language::init()->getLibrary('registration_errors'));
    }

    public function getTitle():array
    {
        return language::init()->getLibrary('registration_field');
    }
}
