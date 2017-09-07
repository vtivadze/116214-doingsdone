<?php

session_start();

unset($_SESSION['email']);
unset($_SESSION['password']);
unset($_SESSION['name']);

header("Location: /116214-doingsdone/");

