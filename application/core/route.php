<?php

class Route
{

	public static function start()
	{
		// контроллер и действие по умолчанию
		$controller_name = 'TasksList';
		$action_name = 'index';

		$routes = explode('/', explode('?', $_SERVER['REQUEST_URI'])[0]);

		// получаем имя контроллера
		if (!empty($routes[1])) {
			$controller_name = $routes[1];
		}
		
		// получаем имя экшена
		if (!empty($routes[2])) {
			$action_name = $routes[2];
		}

		// добавляем префиксы
		$model_name      = 'Model_'      . $controller_name;
		$controller_name = 'Controller_' . $controller_name;
		$action_name     = 'action_'     . $action_name;

		// подцепляем файл с классом модели (файла модели может и не быть)

		$model_file = strtolower($model_name).'.php';
		$model_path = "application/models/" . $model_file;

		if (file_exists($model_path)) {
			include "application/models/" . $model_file;
		}

		// подцепляем файл с классом контроллера
		$controller_file = strtolower($controller_name) . '.php';
		$controller_path = "application/controllers/" . $controller_file;

		if (!file_exists($controller_path)) {
			Route::ErrorPage404();

            $controller_name = 'Controller_404';
            $controller_file = 'controller_404.php';
		}

        include "application/controllers/" . $controller_file;

		// создаем контроллер
        $controller = new $controller_name;
		$action = $action_name;
		
		if (method_exists($controller, $action)) {
			// вызываем действие контроллера
			$controller->$action();
		} else {
			// здесь также разумнее было бы кинуть исключение
			Route::ErrorPage404();
		}
	
	}

	private function ErrorPage404()
	{
        $host = 'https://'.$_SERVER['HTTP_HOST'].'/';
        header('HTTP/1.1 404 Not Found');
		header("Status: 404 Not Found");
		header('Location:'.$host.'404');
    }
    
}
