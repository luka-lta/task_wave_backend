CREATE TABLE `password_resets` (
  `email` varchar(254) NOT NULL,
  `token` char(32) NOT NULL,
  `created_at` datetime NOT NULL,
  `expired_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`email`);
