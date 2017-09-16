<?php

$link = @mysqli_connect('localhost', 'root', '', 'doingsdone');

if (!$link) {
	$error = mysqli_connect_error();
} else {

}