@echo off
:GO
php notify.php
TIMEOUT /T 3600
goto GO