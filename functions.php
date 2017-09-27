<?php
require_once "mysql_helper.php";

/**
 * It returns count of days from task current date to deadline date
 *
 * @param $date_deadline string Task deadline date
 *
 * @return integer days count
 */
function get_days_until_deadline($date_deadline) {
    $current_ts = time();
    $task_deadline_ts = strtotime($date_deadline);
    return floor(($task_deadline_ts - $current_ts) / 86400);
}

/**
 * It renders HTML content for browser
 *
 * @param $template string Template file name
 * @param $params array Data array for inserting in template file
 *
 * @return string HTML content
 */
function render($template, $params = []) {
	$template = 'templates/'.$template.'.php';
	
	if(!is_file($template)) {
        return '';
    }
	extract($params);

	ob_start();
	include $template;
	return ob_get_clean();
}

/**
 * It validates date format dd.mm.yyyy till 2099 using RegExp
 *
 * @param $date string Date formatting as dd.mm.yyyy
 *
 * @return boolean True if format is correct otherwise false
 */
function validateDate($date) {
    return preg_match('/^(0[1-9]|[12][0-9]|3[01])\.(0[1-9]|1[012])\.(19|20)\d\d$/', $date);
}

/**
 * It validates adding task name on max length considering utf-8
 *
 * @param $name string Adding task name
 *
 * @return boolean True in succes and otherwise false
 */
function validateName($taskName) {
    return mb_strlen($taskName) < 15;
}

/**
 * It checks project id existance
 *
 * @param $id integer Project id
 *
 * @return boolean True if project exists and otherwise false
 */
function validateProject($id) {
    return selectData($GLOBALS['con'], 'SELECT id FROM projects WHERE id = ?', [$id]);
}

/**
 * It checks uploaded file type, file size and if file was really uploaded
 *
 * @param $file array Array that contains different kind file info getting form $_FILE
 *
 * @return $result boolean True if file meets the conditions and otherwise false
 */
function validateFile($file) {
    $f_name = $file['name'];
    $f_type = $file['type'];
    $f_size = $file['size'];
    $f_tmp_name = $file['tmp_name'];
    $f_error = $file['error'];

    $mime = ['text/plain', 'application/pdf', 'application/msword', 'text/csv'];

    if (is_uploaded_file($f_tmp_name) &&
        in_array($f_type, $mime) &&
        $f_size < 2000000 &&
        !$f_error)
    {
        $result = true;
    } else {
        $result = false;
    }

    return $result;
}

/**
 * It validates eamil for correctness using php function
 *
 * @param $email string Email
 *
 * @return boolean True if email has a correct format and otherwise false
 */
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

/**
 * It forms array of all errors checking user data sent by form
 *
 * @param &$errors reference An errors container array passed by reference
 * @param $name string Form element name
 * @param $msg string Error relevant message for display to user
 *
 * @return nothing
 */
function formErrors(&$errors, $name, $msg) {
    $errors[$name]['msg'] = $msg;
    $errors[$name]['class'] = 'form__input--error';
}

/**
 * It makes select in DB
 *
 * @param $con mysqli Connection resource
 * @param $sql string Select query ready for binding params
 * @param $params array Params for replacing in select query
 *
 * @return array In success array containing select result and otherwise empty array
 */
function selectData($con, $sql, $params = []) {
    $stmt = db_get_prepare_stmt($con, $sql, $params);
    if (mysqli_stmt_execute($stmt)) {
        $result = $stmt->get_result();
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    } else {
        return [];
    }
}

/**
 * It realize inserting in DB
 *
 * @param $con mysqli Connection resource
 * @param $table string Table name where must be done inserting
 * @param $data array Associative array containing columnName => value pairs
 *
 * @return In succes integer of inserted data id and otherwise boolean false
 */
function insertData($con, $table, $data) {
    $keys = array_keys($data);
    $cols = implode(', ', $keys);
    $values = array_values($data);
    $vals = str_repeat('?, ', count($values));
    $vals = substr($vals, 0, -2);
    $sql = "INSERT INTO $table ($cols) VALUES ($vals)";
    
    $stmt = db_get_prepare_stmt($con, $sql, $values);

    if (mysqli_stmt_execute($stmt)) {
        return mysqli_insert_id($con);
    } else {
        return false;
    }
}

/**
 * It makes arbitrary query to DB
 *
 * @param $con mysqli Connection resource
 * @param $sql string SQL query ready for binding params
 * @param $params array Contains params for replacing in SQL query
 *
 * @return boolean True in success and otherwise false
 */
function arbitraryQuery($con, $sql, $params = []) {
    $stmt = db_get_prepare_stmt($con, $sql, $params);
    return mysqli_stmt_execute($stmt);
}

/**
 * It changes date format from d.m.Y to Y-m-d H:i:s adding time
 *
 * @param $date string Contains date with initial format
 *
 * @return string Date with new format
 */
function formDate($date) {
    $dt = explode('.', $date);
    return $dt[2] . '-' . $dt[1] . '-' . $dt[0] . ' 23:00:00';
}