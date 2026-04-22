<?php

$tasks  = $data['tasks'];
$params = $data['params'];

echo showSort($params);
echo showTasks($tasks);
echo showLinks($params);

function showTasks($tasks)
{
    $html = '
        <div class="tasks_list">';

    if (!empty($tasks)) {
        foreach ($tasks as $key => $task) {

            foreach ($task as $task_key => $task_value) {
                if (!is_array($task_value)) {
                    $task[$task_key] = htmlspecialchars((string) $task_value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
                }
            }

            $csrf = \App\Csrf::field();

            $html .= '
                <br>
                <div class="task">
                    <form enctype="multipart/form-data" method="post" action="/addtask">
                        ' . $csrf . '
                        <input type="hidden" name="id" value="' . $task['id'] . '">
                        <input type="hidden" name="user_name" value="' . $task['user_name'] . '">
                        <input type="hidden" name="status" value="' . $task['status'] . '">
                        
                        <p>Имя пользователя: ' . $task['user_name'] . '</p>
                        <input type="hidden" name="email" value="' . $task['email'] . '">                        
                        <p>email: ' . $task['email'] . '</p>
                        <p>Текст задачи:</p>
                        <textarea readonly="readonly" name="text" cols="100" rows="5">' . $task['text'] . '</textarea>                        
                        <p>Статус: </p><p>' . implode(
                '</p><p>',
                array_map(
                    static fn ($s) => htmlspecialchars((string) $s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'),
                    $task['statuses']
                )
            ) . '</p>                                                                                               
                        ' . (
                            !empty($_SESSION['rules']) &&
                            in_array('edit', $_SESSION['rules'], true) ?
                                '<button type="submit" class="btn btn-primary">Изменить</button>' :
                                ''
                        ) . '                        
                    </form>
                </div>
                <br>';
        }
    }

    $html .= '</div>';

    return $html;
}

/**
 * Прорисовка пагинации
 *
 * @param int $links
 * @return string
 */
function showLinks($params)
{
    $links = $params['links'] ?? 3;
    $page  = (int) $params['page'];
    $limit = (int) $params['limit'];
    $total = (int) $params['total'];

    $get_url = '';

    $params = array_merge($params, $_POST);

    unset($params['page'], $params['_csrf']);

    if (!empty($params)) {
        foreach ($params as $key => $value) {
            if (is_array($value)) {
                continue;
            }
            $get_url .= '&' . rawurlencode((string) $key) . '=' . rawurlencode((string) $value);
        }
    }

    $last = max(1, (int) ceil($total / $limit));

    $start = (($page - $links) > 0) ? $page - $links : 1;
    $end   = (($page + $links) < $last) ? $page + $links : $last;

    $html = '<ul class="pagination">';

    $class = ($page === 1) ? 'disabled' : '';

    $html .= '
        <li class="' . $class . '"><a href="/taskslist?page=1' . $get_url . '">&laquo;</a></li>';

    if ($start > 1) {
        $html .= '
                <li><a href="/taskslist?page=1' . $get_url . '">1</a></li>
                <li class="disabled"><span>...</span></li>';
    }

    for ($i = $start; $i <= $end; $i++) {
        $class = ($page === $i) ? 'active' : '';
        $html .= '
            <li><a class="' . $class . '" href="/taskslist?page=' . $i . $get_url . '">' . $i . '</a></li>';
    }

    if ($end < $last) {
        $html .= '
            <li class="disabled"><span>...</span></li>
            <li><a href="/taskslist?page=' . $last . $get_url . '">' . $last . '</a></li>';
    }

    $class = ($page === $last) ? 'disabled' : '';

    $html .= '
        <li class="' . $class . '"><a href="/taskslist?page=' . $last .
        $get_url . '">&raquo;</a></li>';

    $html .= '</ul>';

    return $html;
}

/**
 *
 */
function showSort($params)
{
    $arr_order = [
        'id'        => 'добавлению',
        'user_name' => 'имени пользователя',
        'email'     => 'email',
        'status'    => 'статусу',
    ];

    $arr_by = [
        'DESC' => 'убывания',
        'ASC'  => 'возрастания',
    ];

    $csrf = \App\Csrf::field();

    $html = '
        <form enctype="multipart/form-data" method="post" action="/taskslist">
            ' . $csrf . '
            <div class="tasks_filter">
                <p>Сортировать по</p>
                <select name="order">
                    ' . getOptions($arr_order, $params['order']) . '
                </select>
                <p>в порядке</p>
                <select name="by">
                    ' . getOptions($arr_by, $params['by']) . '                    
                </select>
                <button class="badge-success" type="submit">Сортировать</button>
            </div> 
        </form>
    ';

    echo $html;
}

function getOptions($arr, $selected = '')
{
    $html = '';

    foreach ($arr as $key => $value) {
        $sel = ($key == $selected) ? 'selected="selected"' : '';
        $html .= '
            <option value="' . htmlspecialchars((string) $key, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '" ' . $sel .
            '>' . htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '</option>';
    }

    return $html;
}
