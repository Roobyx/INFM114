-- ****************** MySQL INFM114 DB ******************;
-- ***********************CREATE*************************;

-- Something along the lines of (changes might have been made since the writing of this):

CREATE TABLE IF NOT EXISTS `infm114`.`users`
(
	`fNumber`  int NOT NULL ,
	`password` varchar(20) NOT NULL,
	PRIMARY KEY(`fNumber`)
);

CREATE TABLE `infm114`.`courses`(
	`signature` VARCHAR(20) NOT NULL,
	`name` VARCHAR(100) NOT NULL,
	`teacherName` VARCHAR(100) NOT NULL,
	`dayOfWeek` TINYINT NOT NULL,
	`time` TIME NOT NULL,
	PRIMARY KEY(`signature`(20))
);

CREATE TABLE `infm114`.`courseworks`(
	`id` INT NOT NULL,
	`dueDate` DATETIME NOT NULL,
	PRIMARY KEY(`id`)
);

CREATE TABLE IF NOT EXISTS `infm114`.`tests`
(
	`id`        int NOT NULL AUTO_INCREMENT ,
	`signature` varchar(20) NOT NULL ,
	`dueDate`   datetime NOT NULL ,

KEY `FK_tests_1` (`signature`),
CONSTRAINT `rel_courses_tests` FOREIGN KEY `FK_tests_1` (`signature`) REFERENCES `infm114`.`courses` (`signature`)
) AUTO_INCREMENT=1;

CREATE TABLE `infm114`.`notes`(
	`id` INT NOT NULL AUTO_INCREMENT,
	`value` TEXT NOT NULL,
	PRIMARY KEY(`id`)
) AUTO_INCREMENT=1;


CREATE TABLE IF NOT EXISTS `infm114`.`student_courses`
(
	`id`        int NOT NULL AUTO_INCREMENT ,
	`fNumber`   int NOT NULL ,
	`signature` varchar(20) NOT NULL ,
	`noteId`    int NULL ,

	PRIMARY KEY (`id`),
	KEY `FK_student_courses_1` (`signature`),
	CONSTRAINT `rel_courses_student_courses` FOREIGN KEY `FK_student_courses_1` (`signature`) REFERENCES `infm114`.`courses` (`signature`),
	KEY `FK_student_courses_2` (`noteId`),
	CONSTRAINT `rel_notes_student_courses` FOREIGN KEY `FK_student_courses_2` (`noteId`) REFERENCES `infm114`.`notes` (`id`),
	KEY `student_fNumber` (`fNumber`),
	CONSTRAINT `rel_users_student_courses` FOREIGN KEY `student_fNumber` (`fNumber`) REFERENCES `infm114`.`users` (`fNumber`)
) AUTO_INCREMENT=1;


CREATE TABLE IF NOT EXISTS `infm114`.`student_courseworks`
(
	`id`           int NOT NULL AUTO_INCREMENT ,
	`fNumber`      int NOT NULL ,
	`courseworkId` int NOT NULL ,
	`noteId`       int NULL ,

PRIMARY KEY (`id`),
KEY `FK_stundet_courseworks_1` (`fNumber`),
CONSTRAINT `rel_users_student_courseworks` FOREIGN KEY `FK_stundet_courseworks_1` (`fNumber`) REFERENCES `infm114`.`users` (`fNumber`),
KEY `FK_stundet_courseworks_2` (`courseworkId`),
CONSTRAINT `rel_courseworks_student_courseworks` FOREIGN KEY `FK_stundet_courseworks_2` (`courseworkId`) REFERENCES `infm114`.`courseworks` (`id`),
KEY `FK_stundet_courseworks_3` (`noteId`),
CONSTRAINT `rel_notes_student_courseworks` FOREIGN KEY `FK_stundet_courseworks_3` (`noteId`) REFERENCES `infm114`.`notes` (`id`)
) AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `infm114`.`student_tests`(
	`id` INT NOT NULL AUTO_INCREMENT,
	`student` INT NOT NULL,
	`testId` INT NOT NULL,
	`noteId` INT NULL,
	PRIMARY KEY(`id`),
	KEY `FK_stundet_tests_1`(`student`),
	CONSTRAINT `rel_users_student_tests` FOREIGN KEY `FK_stundet_tests_1`(`student`) REFERENCES `infm114`.`users`(`fNumber`),
	KEY `FK_stundet_tests_2`(`testId`),
	CONSTRAINT `rel_tests_student_tests` FOREIGN KEY `FK_stundet_tests_2`(`testId`) REFERENCES `infm114`.`tests`(`id`),
	KEY `FK_stundet_tests_3`(`noteId`),
	CONSTRAINT `rel_notes_student_tests` FOREIGN KEY `FK_stundet_tests_3`(`noteId`) REFERENCES `infm114`.`notes`(`id`)
) AUTO_INCREMENT = 1;