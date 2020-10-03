<?php

declare(strict_types=1);

namespace app;

use app\Db;
use http\Exception\InvalidArgumentException;
use mysql_xdevapi\Exception;
use PDO;

class UserData
{
    public int $id;
    public string $name;
    public string $email;
    public string $login;
    public string $password;
    public string $confirmed;
    public int $create_time;

    private Db $db;

    public function __construct()
    {
        $this->db = Db::getInstance();
    }

    public function createUser(string $token):?int
    {
        $stmt = $this->db->connect->prepare("INSERT INTO users(`name`,email,login,password,token,confirmed,create_time) 
        VALUES(:name,:email,:login,:password,:token,:confirmed,:create_time);");

        $stmt->bindValue(':name', $this->name, PDO::PARAM_STR);
        $stmt->bindValue(':email', $this->email, PDO::PARAM_STR);
        $stmt->bindValue(':login', $this->login, PDO::PARAM_STR);
        $stmt->bindValue(':password', $this->passwordHash($this->password), PDO::PARAM_STR);
        $stmt->bindValue(':token', $token, PDO::PARAM_STR);
        $stmt->bindValue(':confirmed', false, PDO::PARAM_BOOL);
        $stmt->bindValue(':create_time', time(), PDO::PARAM_INT);

        if ($stmt->execute()) {
            return (int)$this->db->connect->lastInsertId();
        }
    }

    public function updateUser(bool $confirm = true):bool
    {
        $stmt = $this->db->connect->prepare("UPDATE users SET `name`=:name,email=:email,login=:login,password=:password,confirmed=:confirmed WHERE id=:id LIMIT 1;");
        $stmt->bindValue(':name', $this->name, PDO::PARAM_STR);
        $stmt->bindValue(':email', $this->email, PDO::PARAM_STR);
        $stmt->bindValue(':login', $this->login, PDO::PARAM_STR);
        $stmt->bindValue(':password', $this->password, PDO::PARAM_STR);
        $stmt->bindValue(':confirmed', $confirm, PDO::PARAM_BOOL);
        $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function deleteUser():bool
    {
        $stmt = $this->db->connect->prepare('DELETE FROM users WHERE id=:id LIMIT 1;');
        $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function getUserEmail():?UserData
    {
        $stmt = $this->db->connect->prepare('SELECT id,password FROM users WHERE email=:email AND confirmed=1 LIMIT 1;');
        $stmt->bindValue(':email', $this->email, PDO::PARAM_STR);
        if ($stmt->execute()) {
            $result = (object)$stmt->fetch();
            if ($this->hashValid($this->password, $result->password)) {
                $this->id = (int)$result->id;
                return $this;
            }
        }

        return null;
    }

    public function isEmail():bool
    {
        $stmt = $this->db->connect->prepare('SELECT id FROM users WHERE email=:email LIMIT 1;');
        $stmt->bindValue(':email', $this->email, PDO::PARAM_STR);
        $stmt->execute();
        return !empty($stmt->fetch());
    }

    public function getUserToken(string $token):?UserData
    {
        $stmt = $this->db->connect->prepare('SELECT id FROM users WHERE token=:token;');
        $stmt->bindValue(':token', $token, PDO::PARAM_STR);
        if ($stmt->execute()) {
            $id = $stmt->fetchColumn(0);
            $this->id = (int)$id;
            return $this;
        }
        return null;
    }

    public function getUsers():array
    {
        $stmt =  $this->db->connect->query("SELECT `name` FROM users limit 100");
        if ($stmt) {
            return  $stmt->fetchAll();
        }
        return [];
    }

    public function getUser(int $id):array
    {
        $stmt =  $this->db->connect->prepare("SELECT id,`name`,email,login FROM users WHERE id=:id limit 1");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        if ($stmt->execute()) {
            return  $stmt->fetch();
        }
        return [];
    }
    public function getUserWithPassword(int $id):array
    {
        $stmt =  $this->db->connect->prepare("SELECT id,`name`,email,login,password FROM users WHERE id=:id limit 1");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        if ($stmt->execute()) {
            return  $stmt->fetch();
        }
        return [];
    }
    public function updateUserConfirmed():bool
    {
        $stmt = $this->db->connect->prepare("UPDATE users SET `confirmed`=1 WHERE id=:id LIMIT 1;");
        $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);
        return (bool)$stmt->execute();
    }

    public function passwordHash(string $password):string
    {
        return  password_hash($password, PASSWORD_BCRYPT, ['cost'=>12]);
    }

    private function hashValid(string $inputPassword, string $dbPassword):bool
    {
        if (password_verify($inputPassword, $dbPassword)) {
            return true;
        }
        return false;
    }
}
