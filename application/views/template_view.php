<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="content-type" content="text/html; charset=utf-8"/>
		<meta name="description" content=""/>
		<meta name="keywords" content=""/>

        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

        <link rel="stylesheet" href="/css/bootstrap.min.css" crossorigin="anonymous">

		<link href="https://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet" type="text/css"/>
		<link href="https://fonts.googleapis.com/css?family=Kreon" rel="stylesheet" type="text/css"/>
		<link rel="stylesheet" type="text/css" href="/css/style.css"/>

        <title>Задачи</title>
	</head>
	<body>
		<div id="wrapper">
            <div class="container">

                <div id="header">
                    <hr/>
                    <div class="row pad-15" id="menu">
                        <div class="col-sm">
                            <a href="/taskslist" class="btn btn-lg green">Задания</a>
                        </div>
                        <div class="col-sm">
                            <a href="/addtask" class="btn btn-lg blue">Добавить задание</a>
                        </div>
                        <div class="col-sm">
                            <?php
                            if (empty($_SESSION['user_name'])) {
                                echo '<a href="/login"  class="btn btn-lg red">Войти</a>';
                            } else {
                                if (!empty($_SESSION['rules']) && in_array('edit', $_SESSION['rules'], true)) {
                                    echo '<a href="/changepassword" class="btn btn-lg yellow">Пароль</a> ';
                                }
                                echo '<a href="/logout" class="btn btn-lg red">Выйти</a>';
                            }
                            ?>
                        </div>
                    </div>
                    <hr/>
                </div>

                <div id="page">
                    <div id="content">
                        <div class="box">
                            <?php
                            $flash = \App\Flash::pull();
                            if ($flash !== null) {
                                $type = htmlspecialchars($flash['type'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
                                $msg  = htmlspecialchars($flash['message'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
                                echo '<div class="alert alert-' . $type . '" role="alert">' . $msg . '</div>';
                            }
                            ?>
                            <?php include 'application/views/' . $content_view; ?>
                        </div>
                    </div>
                </div>

                <div id="footer">
                    <br class="clearfix"/>
                    <hr/>
                </div>
            </div>

	</body>
</html>
