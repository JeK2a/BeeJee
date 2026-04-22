<?php

declare(strict_types=1);

class Route
{
    public static function start(): void
    {
        $controller_name = 'TasksList';
        $action_name     = 'index';

        $routes = explode('/', explode('?', $_SERVER['REQUEST_URI'] ?? '/')[0]);

        if (!empty($routes[1])) {
            $controller_name = $routes[1];
        }

        if (!empty($routes[2])) {
            $action_name = $routes[2];
        }

        $model_name      = 'Model_' . $controller_name;
        $controller_name = 'Controller_' . $controller_name;
        $action_name     = 'action_' . $action_name;

        $model_file = strtolower($model_name) . '.php';
        $model_path = 'application/models/' . $model_file;

        if (file_exists($model_path)) {
            require_once 'application/models/' . $model_file;
        }

        $controller_file = strtolower($controller_name) . '.php';
        $controller_path = 'application/controllers/' . $controller_file;

        if (!file_exists($controller_path)) {
            self::errorPage404();

            return;
        }

        require_once 'application/controllers/' . $controller_file;

        if (!class_exists($controller_name, false)) {
            self::errorPage404();

            return;
        }

        $controller = new $controller_name();
        $action     = $action_name;

        if (method_exists($controller, $action)) {
            $controller->$action();
        } else {
            self::errorPage404();
        }
    }

    private static function errorPage404(): void
    {
        http_response_code(404);
        if (!class_exists('Controller_404', false)) {
            require_once 'application/controllers/controller_404.php';
        }
        $c = new Controller_404();
        $c->action_index();
    }
}
