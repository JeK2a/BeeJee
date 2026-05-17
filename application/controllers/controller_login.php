<?php

declare(strict_types=1);

class Controller_Login extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->model = new Model_Login();
        $this->view  = new View();
    }

    public function action_index(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!\App\Csrf::validate($_POST['_csrf'] ?? null)) {
                \App\Flash::error('Недействительная сессия. Обновите страницу и попробуйте снова.');
                $this->redirect('/login');
            }

            if (\App\LoginRateLimiter::isLocked()) {
                $sec = \App\LoginRateLimiter::secondsRemaining();
                \App\Flash::error('Слишком много неудачных попыток. Повторите через ' . $sec . ' с.');
                $this->redirect('/login');
            }

            $userName = trim((string) ($_POST['user_name'] ?? ''));
            $password = (string) ($_POST['password'] ?? '');

            if ($userName === '' || $password === '') {
                \App\Flash::error('Введите имя пользователя и пароль.');
                $this->redirect('/login');
            }

            if ($this->model->verifyCredentials($userName, $password)) {
                \App\LoginRateLimiter::reset();
                session_regenerate_id(true);
                $_SESSION['rules']     = ['edit'];
                $_SESSION['user_name'] = $userName;
                \App\Flash::success('Вы успешно вошли.');
                $this->redirect('/taskslist');
            }

            \App\LoginRateLimiter::recordFailure();
            \App\Flash::error('Неверное имя пользователя или пароль.');
            $this->redirect('/login');
        }

        $this->view->generate('login_view.php', 'template_view.php');
    }

    private function redirect(string $path): void
    {
        header('Location: ' . $path);
        exit;
    }
}
