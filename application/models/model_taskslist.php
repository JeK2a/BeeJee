<?php

use db\DB;

require_once __DIR__ . '/../class/db.php';

class Model_TasksList implements Model
{
    private $db;

    public function __construct()
    {
        $this->db = DB::getConnection();
    }

    public function get_data($params)
    {
        $data = [];
        $data['tasks']   = $this->getTasks($params);
        $params['total'] = $this->getTasksCount();
        $data['params']  = $params;

        return $data;
    }

    private function getTasksCount()
    {
        $query = '
            SELECT COUNT(*) AS `count`
            FROM `task`;';

        $stmt = $this->db->query($query);
        $tasks_count = $stmt->fetchColumn();

        return $tasks_count;
    }

    private function getTasks($params)
    {
        foreach ($params as $key => $value) {
            $params[$key] = addslashes($value);
        }

        $index = $params['page'] - 1;
        $limit = $params['limit'];

        $order = $params['order'] ?? 'id';
        $by    = $params['by']    ?? 'DESC';

        $query = '
            SELECT 
                `id`,
                `user_name`,
                `email`,
                `text`,
                `status`
            FROM `task`
            ORDER BY `' . $order . '` ' . $by . ' 
            LIMIT ' . $index * $limit .',' . $limit;

        $que  = $this->db->query($query);
        $tasks = $que->fetchAll();

        foreach ($tasks as $key => $task) {
            $tasks[$key]['statuses'] = explode('|', $task['status']);
        }
        return $tasks;
    }

    public function set_data($data)
    {
        if (!empty($data['id']) && !in_array('edit', $_SESSION['rules']) ) {
            echo 'Вы должны быть авторизованным для редактирования задачи';
            return false;
        }

        if (
            $data['text_old'] != '' &&
            $data['text_old'] != $data['text']
        ) {
            $data['statuses'][] = 'отредактировано администратором';
        }

        foreach ($data as $key => $value) {
            if (!is_array($value)) {
                $data[$key] = addslashes($value);
            }
        }

        $query = '
            INSERT INTO `task`(
                ' . (empty($data['id']) ? '' : '`id`,') .'
                `user_name`,
                `email`,
                `text`,
                `status`
            ) VALUES (
                ' . (empty($data['id']) ? '' : $data['id'] . ',') .'                
                "' . $data['user_name'] . '",
                "' . $data['email']     . '",
                "' . $data['text']      . '",
                "' . implode('|', $data['statuses'] ?? []) . '"                        
            )
            ON DUPLICATE KEY UPDATE
                `user_name` = VALUES(`user_name`),
                `email`     = VALUES(`email`),
                `text`      = VALUES(`text`),                     
                `status`    = VALUES(`status`)
        ';

        $result  = $this->db->query($query);

        return $result;
    }
}