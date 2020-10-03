<?php


namespace app;

//GET http://127.0.0.1:4000/
//POST http://127.0.0.1:4000/registration

//POST /login
//POST /logout

//GET /users - показать всех users
//POST /users - создать user
//GET /users/{id} - показать user
//PUT /users/{id} - обновить user
//DELETE /users/{id} - удалить user


use app\forms\FormLogin;
use app\forms\FormRegistration;
use app\forms\FormUser;
use app\language\language;

class Route
{
    private string $method;
    private array $methods;
    private string $page;
    private string $id;

    public function __construct()
    {
        try {
            $this->method = strtolower($_SERVER['REQUEST_METHOD']);
            $this->methods = METHODS;
            $this->page = 'index';

            if (!isset($this->methods[$this->method])) {
                throw new \Exception($this->method . ' not supported');
            }

            // $page = explode("/", $_SERVER['REQUEST_URI']);
            // $page = explode("/", substr(@$_SERVER['PATH_INFO'], 1));

            $url = $_SERVER['REQUEST_URI'];
            list($page, $page2, $page3) = sscanf(str_replace(["/","?"], " ", $url), "%s %s %s");

            if (!empty($page)) {
                if (isset($_GET['token'])) {
                    User::getInstance()->verification($_GET['token']);
                    return;
                }

                if (!empty($page) && $this->isPage($page)) {
                    if (($page == 'logout' && !User::getInstance()->isAuth()) ||

                        ($page== 'login' && User::getInstance()->isAuth()) ||

                        ($page == 'users' && isset($page2) && !User::getInstance()->isAuth())
                    ) {
                        require DIR_VIEW . '403.php';
                        return;
                    }

                    if ($page == 'static' && !empty($page2)) {
                        switch ($page2) {
                            case 'registrationForm.js':
                                require DIR_JS.'registrationForm.js';
                                break;
                            case 'userForm.js':
                                require DIR_JS.'userForm.js';
                                break;
                            default:
                                break;
                        }
                        return;
                    } elseif ($page == 'images' && isset($page3) && $page2 == 'users') {
                        switch ($page3) {
                            case 'photo':
                                require DIR_IMAGES.User::getInstance()->getUri().'/photo.jpg';
                                break;
                            default:
                                break;
                        }
                    }


                    $this->page = $page;
                    if (isset($page2)) {
                        $this->id = $page2;
                    }
                } else {
                    require DIR_VIEW . '404.php';
                    return;
                }
            }
            $this->run();

            return;
        } catch (\Throwable $e) {
            // log
            echo $e->getFile()."\n".$e->getMessage();
            exit;
        }
        require DIR_VIEW . '404.php';
    }

    public function run():void
    {
        $method = $this->method;
        $this->$method();
    }

    public function isPage($page):bool
    {
        return array_search($page, ['registration', 'users', 'index', 'login', 'logout','static','images','languages']) === false ? false : true;
    }

    public function get():void
    {
        if ($this->page == 'users') {
            if (!empty($this->id)) {
                $user = User::getInstance()->get($this->id);

                if (!is_null($user)) {
                    $form_user = new FormUser();
                    $library_errors = $form_user->getFieldErrors();
                    $library_fields = $form_user->getTitle();
                    $view = new view\user($user, $library_errors, $library_fields);
                    $view->index();
                } else {
                    require DIR_VIEW . '403.php';
                }
            } else {
                $users = User::getInstance();
                $users = $users->getUsers();
                $view = new view\users($users);
                $view->index();
            }
        } elseif ($this->page == 'registration') {
            $form_registration = new FormRegistration();
            $fields = $form_registration->getFields();
            $library_errors = $form_registration->getFieldErrors();
            $library_fields = $form_registration->getTitle();
            $view = new view\registration($fields, $library_errors, $library_fields);
            $view->index();
        } elseif ($this->page == 'index') {
            $view = new view\index();
            $view->index();
        } else {
            require DIR_VIEW . '404.php';
        }
        return;
    }

    public function post():void
    {
        if ($this->page == 'login') {
            $form_login = new FormLogin(new Validator());

            $errors = $form_login->post($_POST);
            if ($form_login->isValidate()) {
                $userData = new UserData();
                $userData->email = $form_login->email;
                $userData->password = $form_login->password;
                $uid = User::getInstance()->login($userData);
                if (!is_null($uid)) {
                    echo $uid;
                }
            } else {
                echo false;
            }
        } elseif ($this->page == 'logout') {
            User::getInstance()->logout();
            echo true;
        } elseif ($this->page == 'registration') {
            $form_registration = new FormRegistration(new Validator());
            $errors = $form_registration->post($_POST, $_FILES);
            if ($form_registration->isValidate()) {
                $userData = new UserData();
                $userData->email = $form_registration->email;
                if ($userData->isEmail()) {
                    $library_errors = language::init()->getLibrary('registration_errors');
                    echo json_encode(['errors' => ['email'=>[ $library_errors['email_unique'] ]]], 1);
                } else {
                    $userData->name = $form_registration->name;
                    $userData->login = $form_registration->login;
                    $userData->password = $form_registration->password;

                    $result = User::getInstance()->post($userData, $form_registration->file);
                    $library_mail = language::init()->getLibrary('mail');

                    if ($result) {
                        echo json_encode(['messages' => $library_mail['messages']], 1);
                    } else {
                        echo json_encode(['messages' => $library_mail['messages_error']], 1);
                    }
                }
            } else {
                echo json_encode(['errors' => $errors], 1);
            }
        } elseif ($this->page == 'languages') {
            if (isset($_POST['type']) && $_POST['type'] == 'lang' && isset($_POST['languages'])) {
                language::init()->setLanguage($_POST['languages']);
            }
        } elseif ($this->page == 'users') {
            if (!empty($this->id)) {
                $form_user = new FormUser(new Validator());
                $errors = $form_user->put([], $_FILES);
                if ($form_user->isValidate()) {
                    $result = User::getInstance()->putFile($form_user->file);
                    $library_update = language::init()->getLibrary('update');
                    if ($result) {
                        echo json_encode(['messages' => $library_update['messages']], 1);
                    } else {
                        echo json_encode(['messages' => $library_update['messages_error']], 1);
                    }
                } else {
                    echo json_encode(['errors' => $errors], 1);
                }
            }
        }
        exit;
    }

    public function delete():void
    {
        if ($this->page == 'users') {
            if (!empty($this->id)) {
                User::getInstance()->delete();
            }
        }
    }

    public function put():void
    {
        if ($this->page == 'users') {
            if (!empty($this->id)) {
                $form_user = new FormUser(new Validator());
                parse_str(file_get_contents('php://input'), $_PUT);
                $errors = $form_user->put($_PUT);
                if ($form_user->isValidate()) {
                    $userData = new UserData();
                    $userData->email = $form_user->email;
                    $userData->name = $form_user->name;
                    $userData->login = $form_user->login;
                    $userData->password = $form_user->password;
                    $result = User::getInstance()->put($userData);
                    $library_update = language::init()->getLibrary('update');
                    if ($result) {
                        echo json_encode(['messages' => $library_update['messages']], 1);
                    } else {
                        echo json_encode(['messages' => $library_update['messages_error']], 1);
                    }
                } else {
                    echo json_encode(['errors' => $errors], 1);
                }
            }
        }
        exit;
    }
}
