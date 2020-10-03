<?php

declare(strict_types=1);

namespace app;

class Db
{
    private static $instance = null;
    public \PDO $connect;

    private function __construct()
    {
        $this->connect = new  \PDO("mysql:host=127.0.0.1;dbname=dbform;charset=UTF8;port=3306", "jeka", "jeka");
        $this->connect->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $this->connect->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
    }
    private function __clone()
    {
    }
    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new Db();
        }
        return self::$instance;
    }

    /* public function __sleep() {
         return  array('login',  'password');
     }
     public function __wakeup() {
         if(self::$_instance){
             $this->login =  self::$_instance->login;
             $this->password = self::$_instance->password;
         }
     }*/
    public function __serialize(): array
    {
        return [];
    }
    public function __unserialize(array $data)
    {
        $this->connect = new  \PDO("mysql:host=127.0.0.1;dbname=dbform;charset=UTF8;port=3306", "jeka", "jeka");
    }
}
