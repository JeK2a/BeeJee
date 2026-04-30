<?php

declare(strict_types=1);

class Controller_TasksList extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->model = new Model_TasksList();
        $this->view  = new View();
    }

    public function action_index(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['user_name'])) {
            if (!\App\Csrf::validate($_POST['_csrf'] ?? null)) {
                \App\Flash::error('Недействительная сессия. Обновите страницу и попробуйте снова.');
                $this->redirect('/taskslist');
            }

            $ok = $this->model->set_data($_POST);
            if ($ok) {
                \App\Flash::success('Задача сохранена.');
            }
            $this->redirect('/taskslist');
        }

        $params = [
            'page'  => $_GET['page'] ?? 1,
            'limit' => $_GET['limit'] ?? 3,
            'order' => $_POST['order'] ?? $_GET['order'] ?? 'id',
            'by'    => $_POST['by'] ?? $_GET['by'] ?? 'DESC',
        ];

        $data = $this->model->get_data($params);
        $this->view->generate('taskslist_view.php', 'template_view.php', $data);
    }

    private function redirect(string $path): void
    {
        header('Location: ' . $path);
        exit;
    }
}
