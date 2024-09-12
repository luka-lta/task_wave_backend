<?php

declare(strict_types=1);

namespace TaskWaveBackend\Value\User;

class User
{
    private function __construct(
        private readonly ?int             $userId,
        private Role            $role,
        private readonly Username        $username,
        private readonly Email           $email,
        private Password        $password,
        private readonly ?Gender         $gender,
        private readonly ?ProfilePicture $profilePicture
    ) {
    }

    public static function fromDatabase(array $payload): self
    {
        $profilePicture = $payload['profile_picture_path'] === null
            ? null
            : ProfilePicture::fromString($payload['profile_picture_path']);

        $gender = $payload['gender'] === null ? null : Gender::from($payload['gender']);

        return new self(
            $payload['user_id'],
            Role::from($payload['role_id'], $payload['role_name']),
            Username::from($payload['username']),
            Email::from($payload['email']),
            Password::fromHash($payload['password']),
            $gender,
            $profilePicture
        );
    }

    public static function fromRaw(
        ?int $userId,
        int $roleId,
        string $role,
        string  $username,
        string  $email,
        string  $password,
        string $gender = null,
        string $profilePicture = null
    ): self {
        $parsedGender = $gender === null ? null : Gender::from($gender);
        $parsedProfilePicture = $profilePicture === null ? null : ProfilePicture::fromString($profilePicture);

        return new self(
            $userId,
            Role::from($roleId, $role),
            Username::from($username),
            Email::from($email),
            Password::fromPlain($password),
            $parsedGender,
            $parsedProfilePicture
        );
    }

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function getRole(): Role
    {
        return $this->role;
    }

    public function getUsername(): Username
    {
        return $this->username;
    }

    public function getEmail(): Email
    {
        return $this->email;
    }

    public function getPassword(): Password
    {
        return $this->password;
    }

    public function getGender(): ?Gender
    {
        return $this->gender;
    }

    public function getProfilePicture(): ?ProfilePicture
    {
        return $this->profilePicture;
    }

    public function setPassword(Password $password): void
    {
        $this->password = $password;
    }

    public function setRole(Role $role): void
    {
        $this->role = $role;
    }
}
