<?php

class Controller_login extends Controller
{

    function __construct()
    {
        $this->model = new Model_Login();
        $this->view  = new View();
    }
    function action_index()
    {
        if (!empty($_POST)) {
            $user_name = $_POST['user_name'];
            $password  = $_POST['password'];

            if (
                $user_name == 'admin' &&
                $password  == '123'
            ) {
                $_SESSION['rules'][]   = 'edit';
                $_SESSION['user_name'] = $user_name;

                echo '<script>alert("Вы авторизовались");</script>';
            } else {
                echo '<script>alert("Неверный логи или пароль");</script>';
            }
        }

        $this->view->generate('login_view.php', 'template_view.php');
    }

}