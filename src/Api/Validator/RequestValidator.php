<?php

declare(strict_types=1);

namespace TaskWaveBackend\Api\Validator;

class RequestValidator
{
    private array $errors = [];

    public function validate(array $data): array
    {
        foreach ($data as $key => $value) {
            if ($value === null || $value === '') {
                $this->errors[$key] = "$key is required and cannot be empty.";
            }
        }

        return $this->errors;
    }
}
