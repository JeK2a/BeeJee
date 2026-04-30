<?php

declare(strict_types=1);

class Controller_logout extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->model = new Model_Logout();
        $this->view  = new View();
    }

    public function action_index(): void
    {
        if (!empty($_SESSION['user_name'])) {
            unset($_SESSION['rules'], $_SESSION['user_name']);
            \App\Flash::success('Вы вышли из системы.');
        }

        $this->redirect('/taskslist');
    }

    private function redirect(string $path): void
    {
        header('Location: ' . $path);
        exit;
    }
}
