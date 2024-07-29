-- Create database for to-do application
CREATE TABLE `todoapp`.`todolist` 
( 
    `id` INT NOT NULL AUTO_INCREMENT , 
    `title` VARCHAR(255) NOT NULL , 
    `createdAt` TIMESTAMP NOT NULL , PRIMARY KEY (`id`)
) ENGINE = InnoDB;


-- Insert records to the to-do lists
INSERT INTO `todolist` (`id`, `title`, `createdAt`) VALUES 
(NULL, 'Buy groceries', current_timestamp()), 
(NULL, 'Learn GoLang Programming Language', current_timestamp()), 
(NULL, 'Practice dance for 2 hours', current_timestamp()), 
(NULL, 'Learn how to write APIs', current_timestamp());