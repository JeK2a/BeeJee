<?php

declare(strict_types=1);

class Controller_AddTask extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->model = new Model_AddTask();
        $this->view  = new View();
    }

    public function action_index(): void
    {
        if (
            isset($_POST['id']) &&
            !in_array('edit', $_SESSION['rules'] ?? [], true)
        ) {
            \App\Flash::error('Для редактирования необходимо войти как администратор.');
            $this->view->generate('login_view.php', 'template_view.php');

            return;
        }

        $data = is_array($_POST) ? $_POST : [];

        if (isset($data['status']) && is_string($data['status'])) {
            $data['statuses'] = array_filter(explode('|', $data['status']));
        }

        $this->view->generate('addtask_view.php', 'template_view.php', $data);
    }
}
