<?php
session_start();
// unset($_SESSION['email']);
// unset($_SESSION['password']);
//  setcookie('show_completed', '', time() -100000000000);
// exit;
error_reporting(-1);
require_once 'functions.php';
require_once 'userdata.php';

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

require_once 'data.php';

if ($_SERVER['REQUEST_METHOD'] == 'GET') {

    //cookie
    if (isset($_GET['show_completed'])) {
        setcookie('show_completed', $_GET['show_completed']);
        header("Location: /");
        exit;
    }

    //main
    $project = (int)trim($_GET['project'] ?? 0);

    if (!isset($projects[$project])) {
        http_response_code(404);
        exit;
    } else 
        $proj_tasks = get_proj_tasks($projects, $project, $tasks);
        

    //index
    $content = render('index', 
    [
        'show_complete_tasks' => $show_complete_tasks,
        'tasks' => $proj_tasks,
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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
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
            if (call_user_func('validateFile', $_FILES['preview']))
                move_uploaded_file($f_tmp_name, $f_name);
            else
                form_errors($errors, 'preview', 'Некорректный фаил!');
        }

        if (!count($errors)) {
            add_new_task($tasks, $name, $date, $projects[$project], 'Нет');
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
            
            foreach ($users as $user) {
                if ($user['email'] == $email && password_verify($password , $user['password'])) {
                    $_SESSION['email'] = $email;
                    $_SESSION['name'] = $user['name'];

                    header("Location: /");
                    exit;
                } else {
                    $overlay = 'overlay';
                    $hidden = '';
                    $errors['password']['msg'] = "Вы ввели неверный пароль!";
                    $errors['password']['class'] = "form__input--error";

                }
            }

        } else {

            $overlay = 'overlay';
            $hidden = '';

        }
    }

}

$header = render('header', []);

if (!isset($_SESSION['name'])) {
    
    if (isset($_GET['login'])) {
        $overlay = 'overlay';
        $hidden = '';
    }

    $main = render('guest',
    [
        'overlay' => $overlay ?? '',
        'hidden' => $hidden ?? 'hidden',
        'email' => $email ?? '',
        'password' => $password ?? '',
        'errors' => $errors ?? [],
        'header' => $header
    ]);

} else {

    $main = render('layout', 
    [
        'title' => 'Дела в порядке!',
        'projects' => $projects,
        'tasks' => $tasks,
        'overlay' => $overlay ?? '',
        'header' => $header,
        'content' => $content
    ]);
}

ob_start('ob_gzhandler');
echo $main;
ob_end_flush();