<?php
error_reporting(-1);

require_once 'mysql_helper.php';
require_once 'init.php';
require_once 'functions.php';
require_once 'vendor/autoload.php';

$sql = 'SELECT 
			tasks.name, tasks.deadline, users.id, users.name AS uname, users.email
		FROM
			tasks LEFT JOIN
			projects ON tasks.proj_id = projects.id LEFT JOIN
		    users on projects.user_id = users.id
		WHERE
			date_completion IS NULL AND
			TIMESTAMPDIFF(MINUTE, now(), deadline) > 0 AND
			TIMESTAMPDIFF(MINUTE, now(), deadline) <= 60';

$not_done = select_data($con, $sql, []);

$mails = [];
foreach($not_done as $nd) {
	$uid = $nd['id'];
	if (!array_key_exists($uid, $mails)) {
		$mails[$uid] = $nd;
		$mails[$uid]['body'] = sprintf('Уважаемый, %s. У вас запланирована задача %s на %s', $nd['uname'], $nd['name'], $nd['deadline']);
	}
	else {
		$mails[$uid]['body'] .= sprintf(', задача %s на %s', $nd['name'], $nd['deadline']);
	}
}

$transport = (new Swift_SmtpTransport('smtp.mail.ru', 465, 'ssl'))
	->setUsername('doingsdone@mail.ru')
	->setPassword('rds7BgcL')
;
$mailer = new Swift_Mailer($transport);
foreach($mails as $mail) {
	$message = new Swift_Message();
	$message->setTo([$mail['email'] => $mail['name']]);
	$message->setSubject('Уведомление от сервиса «Дела в порядке»');
	$message->setBody($mail['body']);
	$message->setFrom(['doingsdone@mail.ru' => 'DoingsDone']);

	$result = $mailer->send($message);
	var_dump($result);
}