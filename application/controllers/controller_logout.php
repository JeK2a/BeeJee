<?php

class Controller_logout extends Controller
{

    function __construct()
    {
        $this->model = new Model_Logout();
        $this->view  = new View();
    }
    function action_index()
    {
        if (!empty($_SESSION['user_name'])) {
            unset($_SESSION['rules']);
            unset($_SESSION['user_name']);
        }

        $this->view->generate('logout_view.php', 'template_view.php');
    }

}