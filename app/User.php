<?php

declare(strict_types=1);

namespace app;

class User
{
    private static $instance = null;
    private int $id;
    private string $url;
    private bool $auth = false;
    private StoreService $store;

    private function __construct()
    {
        $this->store = new StoreService();
    }

    private function __clone()
    {
    }

    public static function getInstance()
    {
        if (User::$instance == null) {
            User::$instance = new User();
            if (!is_null(User::$instance->store->getCookieValue("UID")) &&
                !is_null(User::$instance->store->getSessionValue(User::$instance->store->getCookieValue("UID")))) {
                User::$instance->id = (int) User::$instance->store->getSessionValue(User::$instance->store->getCookieValue("UID")) ;
                User::$instance->url =  User::$instance->generateUID(User::$instance->id);
                User::$instance->auth = true;
            }
        }
        return  User::$instance;
    }

    public function post(UserData $userData, array $file = null):bool
    {
        $token = md5((string)time());
        $userId = $userData->createUser($token);
        $resultMail = false;
        if (!is_null($userId)) {
            $resultMail = $this->sendMail($userData->email, $userData->name, $token);

            // save photo
            if (!is_null($file)) {
                $finfo = new \finfo(FILEINFO_MIME_TYPE);

                $ext = array_search(
                    $finfo->file($file['files']['tmp_name']),
                    Validator::getPatterns()['file'],
                    true
                );
                if ($ext !== false) {
                    $dir = $this->generateUID($userId);
                    if (mkdir(DIR_IMAGES.$dir)) {
                        move_uploaded_file(
                            $file['files']['tmp_name'],
                            sprintf(
                                DIR_IMAGES.'%s/%s.%s',
                                $dir,
                                'photo',
                                $ext
                            )
                        );
                    }
                }
            }
            // fake mail request
           //$this->verification($token);
        }
        return $resultMail;
    }

    public function put(UserData $userData):bool
    {
        if ($this->auth) {
            $currentUser = $this->getUserWithPassword($this->url);

            $confirmed = ($currentUser['email'] === $userData->email);
            if (!strlen($userData->password)) {
                $userData->password = $currentUser['password'];
            } else {
                $userData->password = $userData->passwordHash($userData->password);
            }
            $userData->id = $this->id;
            return $userData->updateUser($confirmed);
        }
        return false;
    }

    public function putFile(array $file = null):bool
    {
        if ($this->auth) {
            if (!is_null($file)) {
                $finfo = new \finfo(FILEINFO_MIME_TYPE);

                $ext = array_search(
                    $finfo->file($file['files']['tmp_name']),
                    Validator::getPatterns()['file'],
                    true
                );
                if ($ext !== false) {
                    $dir = $this->url;
                    if (!file_exists(DIR_IMAGES.$dir)) {
                        mkdir(DIR_IMAGES.$dir);
                    }
                    if (file_exists(DIR_IMAGES.$dir)) {
                        move_uploaded_file(
                            $file['files']['tmp_name'],
                            sprintf(
                                DIR_IMAGES.'%s/%s.%s',
                                $dir,
                                'photo',
                                $ext
                            )
                        );
                        return true;
                    }
                }
            }
        }
        return false;
    }

    public function delete():bool
    {
        if ($this->auth) {
            $userData = new UserData();
            $userData->id = $this->id;
            if ($userData->deleteUser()) {
                $dir = $this->getUri();
                if (!is_null($dir)) {
                    $this->removeDirectory(DIR_IMAGES.$dir);
                }
                $this->logout();
                return true;
            }
        }
        return false;
    }

    public function get(string $uid):?array
    {
        if ($this->auth && $uid == $this->generateUID($this->id)) {
            return (new UserData())->getUser($this->id);
        }
        return null;
    }

    public function getUserWithPassword(string $uid):?array
    {
        if ($this->auth && $uid == $this->generateUID($this->id)) {
            return (new UserData())->getUserWithPassword($this->id);
        }
        return null;
    }

    public function getUsers():array
    {
        return (new UserData())->getUsers();
    }

    public function logout():void
    {
        if (!is_null($this->store->getCookieValue("UID")) &&
            !is_null($this->store->getSessionValue($this->store->getCookieValue("UID")))) {
            $this->store->unsetSessionValue($this->store->getCookieValue("UID"));

            if (!defined('TEST')) {
                setcookie("UID", "", time()-10000);
            }
            $this->store->unsetCookieValue("UID");
        }
        $this->auth = false;
        unset($this->id);
    }

    public function login(UserData $userData):?string
    {
        $userData = $userData->getUserEmail();
        if (!is_null($userData)) {
            $this->url = $this->generateUID($userData->id);
            $time = time();
            if (!defined('TEST')) {
                setcookie("UID", md5((string)$time.$this->url), time() + 50000, '/');
            } else {
                $this->store->setCookie("UID", md5((string)$time.$this->url));
            }
            $this->store->setSession(md5((string)$time.$this->url), $userData->id);
            $this->auth = true;
            $this->id = $userData->id;
            return $this->url;
        }
        return null;
    }

    public function sendMail($email, $name, $token):bool
    {
        return true;
        /*
         $library_mail = language::init()->getLibrary('mail');

         $mail = new PHPMailer();
         $mail->Timeout = 10;
         $mail->setFrom(ADMIN_EMAIl, ADMIN_NAME);
         $mail->addAddress($email, $name);
         $mail->Subject = SITE_NAME;
         $mail->Body = $library_mail['confirmed'] . ' ' . WEB . '?token=' . $token;
         if (!$mail->send()) {
             return false;
         } else {
             return true;
         }
        */
    }

    public function verification(string $token):void
    {
        $userData = new UserData();
        $userData = $userData->getUserToken($token);

        if (!is_null($userData)) {
            if ($userData->updateUserConfirmed()) {
                $this->url = $this->generateUID($userData->id);
                $time = time();
                if (!defined('TEST')) {
                    setcookie("UID", md5((string)$time.$this->url), time() + 10000, '/');
                } else {
                    $this->store->setCookie("UID", md5((string)$time.$this->url));
                }

                $this->store->setSession(md5((string)$time.$this->url), $userData->id);
                $this->auth = true;
                $this->id = $userData->id;
                header('Location: /users/'.$this->url);
            } else {
                header('Location: /');
            }
        } else {
            header('Location: /');
        }
        exit;
    }

    public function getUri():?string
    {
        return $this->url??null;
    }

    private function generateUID(int $id):string
    {
        return md5((string)$id.SALT_USER_PAGE);
    }

    public function isAuth():bool
    {
        return $this->auth;
    }

    /*private function UIDActivate(string $uid):bool
    {
        if (isset($_COOKIE["UID"]) && isset($_SESSION[$_COOKIE["UID"]])) {
            $this->id = $_SESSION[$_COOKIE["UID"]];
            $this->url = $uid;
            return true;
        }
        return false;
    }*/

    private function removeDirectory($dir)
    {
        if (!defined('TEST')) {
            if ($objs = glob($dir."/*")) {
                foreach ($objs as $obj) {
                    is_dir($obj) ? removeDirectory($obj) : unlink($obj);
                }
            }
            rmdir($dir);
        }
    }

    /**
     * TODO For testing.
     */
    public static function destruct()
    {
        User::$instance = null;
    }
}
