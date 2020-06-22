<?php

class Controller_TasksList extends Controller
{

    function __construct()
    {
        $this->model = new Model_TasksList();
        $this->view  = new View();
    }

    function action_index()
    {
        if (!empty($_POST['user_name'])) {
            $form = $_POST;
            $this->model->set_data($form);

            echo '<script>alert("Задача сохранена");</script>';
        }

        $params = [
            'page'  => $_GET['page']   ?? 1,
            'limit' => $_GET['limit']  ?? 3,
            'order' => $_POST['order'] ?? $_GET['order'] ?? 'id',
            'by'    => $_POST['by']    ?? $_GET['by']    ?? 'DESC',
        ];

        $data = $this->model->get_data($params);
        $this->view->generate('taskslist_view.php', 'template_view.php', $data);
    }

}