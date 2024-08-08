CREATE TABLE `todos`
(
    `todo_id`     int                                        NOT NULL,
    `owner_id`    int                                        NOT NULL,
    `category_id` int                                                 DEFAULT NULL,
    `title`       varchar(30)                                NOT NULL,
    `description` text,
    `deadline`    datetime                                            DEFAULT NULL,
    `priority`    enum ('HIGH','MEDIUM','LOW','NO-PRIORITY') NOT NULL DEFAULT 'NO-PRIORITY',
    `status`      enum ('ToDo','In progress','Finished')     NOT NULL DEFAULT 'ToDo',
    `pinned`      tinyint(1)                                 NOT NULL DEFAULT '0',
    `started_on`  datetime                                            DEFAULT NULL,
    `finished_on` datetime                                            DEFAULT NULL,
    `usaged_time` datetime                                            DEFAULT NULL
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_0900_ai_ci;

ALTER TABLE `todos`
    ADD PRIMARY KEY (`todo_id`),
    ADD KEY `user_id` (`owner_id`),
    ADD KEY `category_id` (`category_id`);

ALTER TABLE `todos`
    MODIFY `todo_id` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `todos`
    ADD CONSTRAINT `category_id` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    ADD CONSTRAINT `user_id` FOREIGN KEY (`owner_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;
