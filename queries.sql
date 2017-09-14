INSERT INTO `projects` (`name`, `user_id`) VALUES 
	('Входящие', 1), ('Учеба', 2), ('Работа', 3), ('Домашние дела', 4), ('Авто', 1);

INSERT INTO `users` (`email`, `name`, `password`) VALUES 
	('ignat.v@gmail.com', 'Игнат', '$2y$10$OqvsKHQwr0Wk6FMZDoHo1uHoXd4UdxJG/5UDtUiie00XaxMHrW8ka'),
	('kitty_93@li.ru', 'Леночка', '$2y$10$bWtSjUhwgggtxrnJ7rxmIe63ABubHQs0AS0hgnOo41IEdMHkYoSVa'),
	('warrior07@mail.ru', 'Руслан', '$2y$10$2OxpEH7narYpkOT1H5cApezuzh10tZEEQ2axgFOaKW.55LxIJBgWW'),
	('vaxotivadze@gmail.com', 'Вахтанг', '$2y$10$1xDJ5h.LL9Qd4t/qtCR/d.j4wepIH3sPMtetgAUBDt1ucyc3SQJ0y');


INSERT INTO `tasks` (`date_creation`, `name`, `deadline`, `proj_id`) VALUES
	('25.05.2018', 'Собеседование в IT компании', '01.06.2018', 3);

INSERT INTO `tasks` (`date_creation`, `name`, `deadline`, `proj_id`) VALUES
	('24.05.2018', 'Выполнить тестовое задание', '25.05.2018', 3);

INSERT INTO `tasks` (`date_creation`, `date_completion`, `name`, `deadline`, `proj_id`) VALUES
	('15.04.2018', '20.04.2018' , 'Сделать задание первого раздела', '21.04.2018', 2);	

INSERT INTO `tasks` (`date_creation`, `name`, `deadline`, `proj_id`) VALUES
	('20.04.2018','Встреча с другом', '22.04.2018', 1);

INSERT INTO `tasks` (`date_creation`, `name`, `proj_id`) VALUES
	('18.03.2018', 'Купить корм для кота', 4);

INSERT INTO `tasks` (`date_creation`, `name`,`proj_id`) VALUES
	('10.02.2018', 'Заказать пиццу', 1);



-- list of all projects of a user
SELECT `name` FROM `projects` WHERE `user_id` = 'user_id';

-- list of all tasks from some project
SELECT * FROM `tasks` WHERE `proj_id` = 'proj_id';

-- sett a task as completed
UPDATE `tasks` SET date_completion = CURRENT_TIMESTAMP() WHERE `id` = 'id';

-- list of all tasks for tomorrow
SELECT * FROM `tasks` WHERE `deadline` = CURDATE() + INTERVAL 1 DAY;

-- update task's name by task's id
UPDATE `tasks` SET `name` = 'new name' WHERE `id` = 'id';