CREATE TABLE `categories`
(
    `category_id` int         NOT NULL,
    `owner_id`    int         NOT NULL,
    `name`        varchar(20) NOT NULL,
    `description` text,
    `color`       char(7)     NOT NULL DEFAULT '707070'
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_0900_ai_ci;

ALTER TABLE `categories`
    ADD PRIMARY KEY (`category_id`),
    ADD KEY `owner_id` (`owner_id`);

ALTER TABLE `categories`
    MODIFY `category_id` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `categories`
    ADD CONSTRAINT `owner_id` FOREIGN KEY (`owner_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;
