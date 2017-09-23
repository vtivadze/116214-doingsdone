<?php

$content = file_get_contents('composer.json');
//echo $content; exit;
if (strpos($content, '\n')) {
    echo 'yes';
}
else {
    echo 'no';
}

//$content = str_replace('\n', '\r\n', $content);

exit;
session_start();
error_reporting(-1);

require_once 'mysql_helper.php';
require_once 'init.php';
require_once 'functions.php';
require_once 'vendor/autoload.php';

// показывать или нет выполненные задачи
$show_complete_tasks = $_COOKIE['show_completed'] ?? 0;

// устанавливаем часовой пояс в Московское время
date_default_timezone_set('Europe/Moscow');

$days = rand(-3, 3);
$task_deadline_ts = strtotime("+" . $days . " day midnight"); // метка времени даты выполнения задачи
$current_ts = strtotime('now midnight'); // текущая метка времени

// запишите сюда дату выполнения задачи в формате дд.мм.гггг
$date_deadline = date('d.m.Y', $task_deadline_ts);

// в эту переменную запишите кол-во дней до даты задачи
$days_until_deadline = floor(($task_deadline_ts - $current_ts) / 86400);

$projects = select_data($con, 'SELECT projects.*, count(tasks.proj_id) AS `count` FROM projects LEFT JOIN tasks ON projects.id = tasks.proj_id GROUP BY projects.id', []);
$tasks = select_data($con, 'SELECT * FROM tasks', []);

if ($_SERVER['REQUEST_METHOD'] == 'GET' && $con) {

    //cookie
    if (isset($_GET['show_completed'])) {
        setcookie('show_completed', $_GET['show_completed']);
        header("Location: /");
        exit;
    }

    if (isset($_GET['rm'])) {
        arbitrary_query($con, 'UPDATE tasks SET date_completion = now() WHERE id = ?', [$_GET['rm']]);
        header("Location: /");
        exit;
    }

    //main
    $project = (int)trim($_GET['project'] ?? 0);

    if ($project != 0 && !select_data($con, 'SELECT id FROM projects WHERE id = ?', [$project])) {
        http_response_code(404);
        exit;
    } else {
        if ($project != 0) {
            $proj_tasks = select_data($con, 'SELECT * FROM tasks WHERE proj_id = ?', [$project]);
        }
        else {
             $proj_tasks = select_data($con, 'SELECT * FROM tasks', []);
        }
    }
    
    if (isset($_GET['tasks'])) {
        switch($_GET['tasks']) {
            case 'today':
                $where = ' WHERE deadline = DATE(NOW())';
                break;
            case 'tomorrow':
                $where = ' WHERE deadline = DATE(SUBDATE(NOW(), INTERVAL -1 DAY))';
                break;
            case 'expired':
                $where = ' WHERE deadline < DATE(now()) AND date_completion IS NULL';
                break;
            default:
                $where = '';
        }
        $proj_tasks = select_data($con, 'SELECT * FROM tasks' . $where, []);
    }
    //index
    $content = render('index', 
    [
        'show_complete_tasks' => $show_complete_tasks,
        'tasks' => $proj_tasks,
        'show' => $_GET['tasks'] ?? 'all'
    ]);

    //add task
    if (isset($_GET['add'])) {
        $overlay = 'overlay';
        $content = render('add_task', 
        [
            'projects' => $projects,
            'project' => '',
            'name' => '',
            'date' => '',
            'errors' => [],
        ]);
    }

}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && $con) {
    if (isset($_POST['add'])) {

        $name = trim($_POST['name']);
        $project = trim($_POST['project']);
        $date = trim($_POST['date']);

        $required = ['name', 'project', 'date'];
        $rules = 
        [
            'name' => 'validateName',
            'project' => 'validateProject',
            'date' => 'validateDate',
        ];

        $errors = [];
        foreach ($_POST as $key => $value) {
            if (in_array($key, $required) && $value == '') {
                form_errors($errors, $key, 'Заполните это поле!');
                continue;
            }

            if (array_key_exists($key, $rules)) {
                if (!call_user_func($rules[$key], $value))
                    form_errors($errors, $key, 'Заполните поле корректно!');
            }
        }

        if (isset($_FILES['preview']) && $_FILES['preview']['error'] != 4) {
            if (call_user_func('validateFile', $_FILES['preview'])) {
                $f_name = $_FILES['preview']['name'];
                $f_tmp_name = $_FILES['preview']['tmp_name'];
                move_uploaded_file($f_tmp_name, $f_name);
            }
            else
                form_errors($errors, 'preview', 'Некорректный фаил!');
        }

        if (!count($errors)) {
            $res = insert_data($con, 'tasks', [
                'name' => $name,
                'file' => $f_name ?? null,
                'deadline' => $date ? form_date($date) : null,
                'proj_id' => (int)$project
            ]);

            header("Location: /");
            exit;

            $content = render('index', 
            [
                'show_complete_tasks' => $show_complete_tasks,
                'tasks' => $tasks
            ]);
        } else {

            $overlay = 'overlay';
            $content = render('add_task', 
            [
                'projects' => $projects,
                'project' => $project ?? '',
                'name' => $name ?? '',
                'date' => $date ?? '',
                'errors' => $errors ?? []
            ]);

        }
    }

    if (isset($_POST['login'])) {

        $email = trim($_POST['email']);
        $password = trim($_POST['password']);

        $required = ['email', 'password'];
        $rules = ['email' => 'validateEmail'];

        $errors = [];
        foreach ($_POST as $key => $value) {
            if (in_array($key, $required) && $value == '') {
                form_errors($errors, $key, 'Заполните это поле!');
                continue;
            }

            if (array_key_exists($key, $rules)) {
                if (!call_user_func($rules[$key], $value))
                    form_errors($errors, $key, 'Заполните поле корректно!');
            }
        }

        if (!count($errors)) {
            
            $user_dt = select_data($con, 'SELECT name, password FROM users WHERE email = ?', [$email]);
            
            if ($user_dt && password_verify($password, $user_dt[0]['password'])) {
                $_SESSION['email'] = $email;
                $_SESSION['name'] = $user_dt[0]['name'];

                header("Location: /");
                exit;
            } else {
                $overlay = 'overlay';
                $hidden = '';
                $errors['password']['msg'] = "Вы ввели неверный пароль!";
                $errors['password']['class'] = "form__input--error";

            }

        } else {

            $overlay = 'overlay';
            $hidden = '';

        }
    }

    if (isset($_POST['register'])) {
        
        $email = trim($_POST['email']);
        $password = trim($_POST['password']);
        $name = trim($_POST['name']);

        $required = ['email', 'password', 'name'];
        $rules = ['email' => 'validateEmail'];

        $errors = [];
        foreach ($_POST as $key => $value) {
            if (in_array($key, $required) && $value == '') {
                form_errors($errors, $key, 'Заполните это поле!');
                continue;
            }

            if (array_key_exists($key, $rules)) {
                if (!call_user_func($rules[$key], $value))
                    form_errors($errors, $key, 'Заполните поле корректно!');
            }
        }


        if (select_data($con, 'SELECT id FROM users WHERE email = ?', [$email])) {
            $errors['email'] = [
                'class' => 'form__input--error',
                'msg' => 'Email используется другим пользователем!'
            ];
        }

        if (!count($errors)) {
            
            insert_data($con, 'users', [
                'email' => $email,
                'name' => $name,
                'password' => password_hash($password, PASSWORD_DEFAULT)
            ]); 

            $registered = select_data($con, 'SELECT name, password FROM users WHERE email = ?', [$email]);

        }
    }
}

$header = render('header', []);

if (isset($error)) {
    $err_cont = render('error', ['error' => $error]);
    $overlay = '';
    $hidden = 'hidden';
}

if (!isset($_SESSION['name'])) {
    
    if ((isset($_GET['login']) && !isset($error)) || isset($registered)) {
        $overlay = 'overlay';
        $hidden = '';
    }

    $content = render('guest_cont', []);

    $main = render('guest',
    [
        'overlay' => $overlay ?? '',
        'hidden' => $hidden ?? 'hidden',
        'email' => $email ?? '',
        'password' => $password ?? '',
        'errors' => $errors ?? [],
        'header' => $header,
        'content' => $err_cont ?? $content,
        'registered' => $registered ?? false
    ]);

    if (isset($_GET['register']) || (isset($_POST['register']) && !isset($registered))) {
        $main = render('register', [
            'email' => $email ?? '',
            'password' => $password ?? '',
            'name' => $name ?? '',
            'errors' => $errors ?? []
        ]);
    }


} else {

    $main = render('layout', 
    [
        'title' => 'Дела в порядке!',
        'projects' => $projects,
        'tasks' => $tasks,
        'overlay' => $overlay ?? '',
        'header' => $header,
        'content' => $err_cont ?? $content
    ]);
}

ob_start('ob_gzhandler');
echo $main;
ob_end_flush();