<?php

declare(strict_types=1);

use db\DB;

require_once dirname(__DIR__) . '/class/DB.php';

class Model_TasksList implements Model
{
    private const ALLOWED_ORDER = ['id', 'user_name', 'email', 'status'];
    private const ALLOWED_BY    = ['ASC', 'DESC'];
    private const MAX_TEXT_LEN  = 20000;
    private const MAX_NAME_LEN  = 255;
    private const MAX_EMAIL_LEN = 255;

    private \PDO $db;

    public function __construct()
    {
        $this->db = DB::getConnection();
    }

    public function get_data($params)
    {
        $page  = max(1, (int) ($params['page'] ?? 1));
        $limit = max(1, min(100, (int) ($params['limit'] ?? 3)));
        $order = (string) ($params['order'] ?? 'id');
        $by    = strtoupper((string) ($params['by'] ?? 'DESC'));

        if (!in_array($order, self::ALLOWED_ORDER, true)) {
            $order = 'id';
        }
        if (!in_array($by, self::ALLOWED_BY, true)) {
            $by = 'DESC';
        }

        $offset = ($page - 1) * $limit;

        $countStmt = $this->db->query('SELECT COUNT(*) FROM task');
        $total     = (int) $countStmt->fetchColumn();

        $quotedOrder = '`' . str_replace('`', '', $order) . '`';
        $driver       = DB::driver();
        if ($driver === 'sqlite') {
            $quotedOrder = '"' . str_replace('"', '', $order) . '"';
        }

        $sql = 'SELECT id, user_name, email, text, status FROM task ORDER BY ' . $quotedOrder . ' ' . $by
            . ' LIMIT ' . (int) $limit . ' OFFSET ' . (int) $offset;

        $stmt  = $this->db->query($sql);
        $tasks = $stmt->fetchAll();

        foreach ($tasks as $key => $task) {
            $tasks[$key]['statuses'] = explode('|', (string) $task['status']);
        }

        return [
            'tasks'  => $tasks,
            'params' => [
                'page'  => $page,
                'limit' => $limit,
                'order' => $order,
                'by'    => $by,
                'total' => $total,
            ],
        ];
    }

    public function set_data($data)
    {
        if (!is_array($data)) {
            return false;
        }

        if (!empty($data['id']) && !in_array('edit', $_SESSION['rules'] ?? [], true)) {
            \App\Flash::error('Для редактирования необходимо войти как администратор.');

            return false;
        }

        $userName = $this->sanitizeString($data['user_name'] ?? '', self::MAX_NAME_LEN);
        $email    = $this->sanitizeEmail($data['email'] ?? '');
        $text     = $this->sanitizeString($data['text'] ?? '', self::MAX_TEXT_LEN);
        $id       = isset($data['id']) ? (int) $data['id'] : 0;

        if ($userName === '' || $email === '' || $text === '') {
            \App\Flash::error('Заполните имя, email и текст задачи.');

            return false;
        }

        $statuses = [];
        if (!empty($data['statuses']) && is_array($data['statuses'])) {
            foreach ($data['statuses'] as $s) {
                if ($s === 'выполнено') {
                    $statuses[] = 'выполнено';
                }
            }
        }

        if (
            !empty($data['text_old']) &&
            is_string($data['text_old']) &&
            $data['text_old'] !== '' &&
            $data['text_old'] !== $text
        ) {
            $statuses[] = 'отредактировано администратором';
        }

        $statusStr = implode('|', array_unique($statuses));

        try {
            if ($id > 0) {
                $this->upsertWithId($id, $userName, $email, $text, $statusStr);
            } else {
                $this->insertNew($userName, $email, $text, $statusStr);
            }
        } catch (\PDOException $e) {
            error_log('Task save failed: ' . $e->getMessage());
            \App\Flash::error('Не удалось сохранить задачу.');

            return false;
        }

        return true;
    }

    private function insertNew(string $userName, string $email, string $text, string $status): void
    {
        $stmt = $this->db->prepare(
            'INSERT INTO task (user_name, email, text, status) VALUES (?, ?, ?, ?)'
        );
        $stmt->execute([$userName, $email, $text, $status]);
    }

    private function upsertWithId(int $id, string $userName, string $email, string $text, string $status): void
    {
        if (DB::driver() === 'mysql') {
            $stmt = $this->db->prepare(
                'INSERT INTO task (id, user_name, email, text, status) VALUES (?, ?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE user_name = VALUES(user_name), email = VALUES(email),
                text = VALUES(text), status = VALUES(status)'
            );
            $stmt->execute([$id, $userName, $email, $text, $status]);

            return;
        }

        $stmt = $this->db->prepare(
            'INSERT INTO task (id, user_name, email, text, status) VALUES (?, ?, ?, ?, ?)
            ON CONFLICT(id) DO UPDATE SET user_name = excluded.user_name, email = excluded.email,
            text = excluded.text, status = excluded.status'
        );
        $stmt->execute([$id, $userName, $email, $text, $status]);
    }

    private function sanitizeString(string $value, int $maxLen): string
    {
        $value = trim($value);

        return mb_substr($value, 0, $maxLen, 'UTF-8');
    }

    private function sanitizeEmail(string $email): string
    {
        $email = trim(mb_substr($email, 0, self::MAX_EMAIL_LEN, 'UTF-8'));
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return '';
        }

        return $email;
    }
}
