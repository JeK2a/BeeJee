<?php

class Controller_AddTask extends Controller
{

    function __construct()
    {
        $this->model = new Model_AddTask();
        $this->view  = new View();
    }
    function action_index()
    {
        if (
            isset($_POST['id']) &&
            !in_array('edit', $_SESSION['rules'] ?? [])
        ) {
            $this->view->generate('login_view.php', 'template_view.php');
            echo '<script>alert("Для редактирования необходимо авторизоваться");</script>';
            return;
        }

        $data = $_POST;

        $data['statuses'] = explode('|', $data['status']);

        $this->view->generate('addtask_view.php', 'template_view.php', $data);
    }

}