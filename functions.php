<?php
function get_tasks_count($tasks, $project) {
    if (strtolower($project) == strtolower('Все')) {
        $count = count($tasks);
    } else {
        $count = 0;
        foreach($tasks as $t)
            if(strtolower($t['Категория']) == strtolower($project))
                $count++;
    }
    return $count;
}

function get_days_until_deadline($date_deadline) {
    $current_ts = time();
    $task_deadline_ts = strtotime($date_deadline);

    return floor(($task_deadline_ts - $current_ts) / 86400);
}

function render($template, $params = []) {
	$template = 'templates/'.$template.'.php';
	
	if(!is_file($template)) return '';
	extract($params);

	ob_start();
	include $template;
	return ob_get_clean();
}

function validateDate($value) {
    return preg_match('/^(0[1-9]|[12][0-9]|3[01])\.(0[1-9]|1[012])\.(19|20)\d\d$/', $value) && (strtotime($value) > time());
}

function validateName($value) {
    return mb_strlen($value) < 15;
}

function validateProject($value) {
    return array_key_exists($value, $GLOBALS['projects']);
}

function validateFile($value) {
    $f_name = $value['name'];
    $f_type = $value['type'];
    $f_size = $value['size'];
    $f_tmp_name = $value['tmp_name'];
    $f_error = $value['error'];

    $mime = ['text/plain', 'application/pdf', 'application/msword', 'text/csv'];

    if (is_uploaded_file($f_tmp_name) &&
        in_array($f_type, $mime) &&
        $f_size < 2000000 &&
        !$f_error) {
        $result = true;
    } else {
        $result = false;
    }

    return $result;
}