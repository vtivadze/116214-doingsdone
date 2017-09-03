<?php
error_reporting(-1);
require_once 'functions.php';
// показывать или нет выполненные задачи
$show_complete_tasks = rand(0, 1);

// устанавливаем часовой пояс в Московское время
date_default_timezone_set('Europe/Moscow');

$days = rand(-3, 3);
$task_deadline_ts = strtotime("+" . $days . " day midnight"); // метка времени даты выполнения задачи
$current_ts = strtotime('now midnight'); // текущая метка времени

// запишите сюда дату выполнения задачи в формате дд.мм.гггг
$date_deadline = date('d.m.Y', $task_deadline_ts);

// в эту переменную запишите кол-во дней до даты задачи
$days_until_deadline = floor(($task_deadline_ts - $current_ts) / 86400);

//projects
$projects = ['Все', 'Входящие', 'Учеба', 'Работа', 'Домашние дела', 'Авто'];
//tasks
$tasks = [
    [
        'Задача' => 'Собеседование в IT компании',
        'Дата выполнения' => '01.06.2018',
        'Категория' => 'Работа',
        'Выполнен' => 'Нет'
    ],
    [
        'Задача' => 'Выполнить тестовое задание',
        'Дата выполнения' => '25.05.2018',
        'Категория' => 'Работа',
        'Выполнен' => 'Нет'
    ],
    [
        'Задача' => 'Сделать задание первого раздела',
        'Дата выполнения' => '21.04.2018',
        'Категория' => 'Учеба',
        'Выполнен' => 'Да'
    ],
    [
        'Задача' => 'Встреча с другом',
        'Дата выполнения' => '28.08.2017',// '22.04.2018',
        'Категория' => 'Входящие',
        'Выполнен' => 'Нет'
    ],
    [
        'Задача' => 'Купить корм для кота',
        'Дата выполнения' => 'Нет',
        'Категория' => 'Домашние дела',
        'Выполнен' => 'Нет'
    ],
    [
        'Задача' => 'Заказать пиццу',
        'Дата выполнения' => 'Нет',
        'Категория' => 'Домашние дела',
        'Выполнен' => 'Нет'
    ]
];

if($_SERVER['REQUEST_METHOD'] == 'GET') {

    //main
    $project = $_GET['project'] ?? 0;
    $proj_tasks = [];
    if(array_key_exists($project, $projects)) {
        foreach ($tasks as $key => $value) {
            if ($value['Категория'] === $projects[$project] || $project == 0) {
                $proj_tasks[] = $tasks[$key];
            }
        }
    } else {
        http_response_code(404);
        exit;
    }

    //index
    $content = render('index', [
        'show_complete_tasks' => $show_complete_tasks,
        'tasks' => $proj_tasks,
    ]);

    //add task
    if (isset($_GET['add'])) {
        $overlay = ' class="overlay"';
        $content = render('add_task', [
            'projects' => $projects,
            'errors' => [],
        ]);
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add'])) {
        $name = htmlspecialchars(trim($_POST['name']));
        $project = htmlspecialchars(trim($_POST['project']));
        $date = htmlspecialchars(trim($_POST['date']));
        
        $required = ['name', 'project', 'date'];
        $rules = [
            'name' => 'validateName',
            'project' => 'validateProject',
            'date' => 'validateDate',
        ];
        $errors = [];

        foreach ($_POST as $key => $value) {
            if (in_array($key, $required) && $value == '') {
                $errors[$key] = "Заполните это поле!";
                continue;
            }

            if (array_key_exists($key, $rules)) {
                $result = call_user_func($rules[$key], $value);

                if (!$result) {
                    $errors[$key] = "Заполните поле корректно!";
                }
            }
        }

        if (isset($_FILES['preview'])) {
            $result = call_user_func('validateFile', $_FILES['preview']);

            if ($result) {
                move_uploaded_file($f_tmp_name, $f_name);
            } else {
                $errors['preview'] = "Никорректный фаил!";
            }
        }

        if (!count($errors)) {

            array_unshift($tasks, [
                'Задача' => $name,
                'Дата выполнения' => $date,
                'Категория' => $projects[$project],
                'Выполнен' => 'Нет'
            ]);

            $content = render('index', [
                'show_complete_tasks' => $show_complete_tasks,
                'tasks' => $tasks,
            ]);

        } else {

            $overlay = ' class="overlay"';
            $content = render('add_task', [
                'projects' => $projects,
                'errors' => $errors,
                'name' => $name,
                'project' => $project,
                'date' => $date
            ]);

        }
    }
}

ob_start('ob_gzhandler');
echo render('layout', [
    'title' => 'Дела в порядке!',
    'projects' => $projects,
    'tasks' => $tasks,
    'overlay' => $overlay ?? '',
    'content' => $content
]);
ob_end_flush();