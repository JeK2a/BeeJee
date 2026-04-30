<?php

declare(strict_types=1);

class Controller_ChangePassword extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->model = new Model_ChangePassword();
        $this->view  = new View();
    }

    public function action_index(): void
    {
        if (empty($_SESSION['user_name']) || !in_array('edit', $_SESSION['rules'] ?? [], true)) {
            \App\Flash::error('Смена пароля доступна только после входа администратором.');
            $this->redirect('/login');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!\App\Csrf::validate($_POST['_csrf'] ?? null)) {
                \App\Flash::error('Недействительная сессия. Обновите страницу и попробуйте снова.');
                $this->redirect('/changepassword');
            }

            $userName = (string) $_SESSION['user_name'];
            $current  = (string) ($_POST['current_password'] ?? '');
            $new      = (string) ($_POST['new_password'] ?? '');
            $confirm  = (string) ($_POST['new_password_confirm'] ?? '');

            $code = $this->model->changePassword($userName, $current, $new, $confirm);
            if ($code === '') {
                \App\LoginRateLimiter::reset();
                \App\Flash::success('Пароль успешно изменён.');
                $this->redirect('/taskslist');
            }

            $messages = [
                'mismatch'   => 'Новый пароль и подтверждение не совпадают.',
                'weak'       => 'Новый пароль должен быть не короче 8 символов.',
                'current'    => 'Текущий пароль указан неверно.',
                'not_found'  => 'Пользователь не найден в базе.',
                'same'       => 'Новый пароль должен отличаться от текущего.',
                'db'         => 'Не удалось сохранить пароль. Попробуйте позже.',
            ];
            \App\Flash::error($messages[$code] ?? 'Не удалось сменить пароль.');

            $this->redirect('/changepassword');
        }

        $this->view->generate('changepassword_view.php', 'template_view.php');
    }

    private function redirect(string $path): void
    {
        header('Location: ' . $path);
        exit;
    }
}
