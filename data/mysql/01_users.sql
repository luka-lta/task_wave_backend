CREATE TABLE `users`
(
    `user_id`              int          NOT NULL,
    `role_id`              int          NOT NULL          DEFAULT '4',
    `email`                varchar(254) NOT NULL,
    `username`             varchar(32)  NOT NULL,
    `password`             varchar(255) NOT NULL,
    `gender`               enum ('Male','Female','Other') DEFAULT NULL,
    `profile_picture_path` int                            DEFAULT NULL,
    `last_logged_in`       datetime                       DEFAULT NULL,
    `updated_on`           datetime                       DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    `disabled`             tinyint(1)   NOT NULL          DEFAULT '0',
    `banned`               tinyint(1)   NOT NULL          DEFAULT '0'
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_0900_ai_ci;

ALTER TABLE `users`
    ADD PRIMARY KEY (`user_id`),
    ADD KEY `role` (`role_id`);

ALTER TABLE `users`
    MODIFY `user_id` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `users`
    ADD CONSTRAINT `role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
