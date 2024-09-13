CREATE TABLE roles
(
    id   INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL
);

CREATE TABLE permissions
(
    id   INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL
);

CREATE TABLE role_permissions
(
    role_id       INT,
    permission_id INT,
    FOREIGN KEY (role_id) REFERENCES roles (id),
    FOREIGN KEY (permission_id) REFERENCES permissions (id),
    PRIMARY KEY (role_id, permission_id)
);

INSERT INTO roles (name)
VALUES ('Admin'),
       ('Editor'),
       ('Reader'),
       ('User');

INSERT INTO permissions (name)
VALUES ('read'),
       ('write'),
       ('delete');

INSERT INTO role_permissions (role_id, permission_id)
VALUES (1, 1),
       (1, 2),
       (1, 3);

INSERT INTO role_permissions (role_id, permission_id)
VALUES (2, 1),
       (2, 2);

INSERT INTO role_permissions (role_id, permission_id)
VALUES (3, 1);
