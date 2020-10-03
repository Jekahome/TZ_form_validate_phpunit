<?php

declare(strict_types=1);

namespace app\forms;

use app\AbstractValidator;
use app\AbstractForm;
use app\language\language;
use app\ManagerValidator;
use app\BuildFilter;
use app\filters\FilterAnd;
use app\filters\FilterOr;
use app\IValidator;
use app\Validator;

class FormUser implements AbstractForm
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

    public function put(array $input, array $file = null): array
    {
        try {
            if (is_null($file)) {
                $this->name = $input['name'];
                $this->email = $input['email'];
                $this->login = $input['login'];
                $this->password = $input['password'];
                if (strlen($this->password)) {
                    $this->confirmPassword = $input['confirmPassword'];
                }
                $this->validator->activateField('name', $this->name);
                $this->validator->activateField('login', $this->login);
                $this->validator->activateField('email', $this->email);

                if (strlen($this->password)) {
                    $this->validator->activateField('password', $this->password);
                    $this->validator->activateField('confirmPassword', $this->confirmPassword);
                }
            } else {
                $this->file = $file;
                $this->validator->activateField('file', $this->file);
            }
            return $this->validate();
        } catch (\Throwable $e) {
            return [];
        }
    }

    public function validate(): array
    {
        return $this->validator->validate();
    }

    public function isValidate(): bool
    {
        return $this->validator->isValidate();
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
