<?php
declare(strict_types=1);

namespace App\Core;

class Validator
{
    private array $data;
    private array $errors = [];

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public static function make(array $data): self
    {
        return new self($data);
    }

    public function required(string $field, string $message = null): self
    {
        if (!isset($this->data[$field]) || empty(trim($this->data[$field]))) {
            $this->errors[$field] = $message ?? "The $field field is required";
        }
        
        return $this;
    }

    public function email(string $field, string $message = null): self
    {
        if (isset($this->data[$field]) && !filter_var($this->data[$field], FILTER_VALIDATE_EMAIL)) {
            $this->errors[$field] = $message ?? "The $field field must be a valid email";
        }
        
        return $this;
    }

    public function date(string $field, string $message = null): self
    {
        if (isset($this->data[$field])) {
            $date = \DateTime::createFromFormat('Y-m-d', $this->data[$field]);
            if (!$date || $date->format('Y-m-d') !== $this->data[$field]) {
                $this->errors[$field] = $message ?? "The $field field must be a valid date (YYYY-MM-DD)";
            }
        }
        
        return $this;
    }

    public function in(string $field, array $values, string $message = null): self
    {
        if (isset($this->data[$field]) && !in_array($this->data[$field], $values)) {
            $this->errors[$field] = $message ?? "The $field field must be one of: " . implode(', ', $values);
        }
        
        return $this;
    }

    public function minLength(string $field, int $min, string $message = null): self
    {
        if (isset($this->data[$field]) && strlen($this->data[$field]) < $min) {
            $this->errors[$field] = $message ?? "The $field field must be at least $min characters";
        }
        
        return $this;
    }

    public function maxLength(string $field, int $max, string $message = null): self
    {
        if (isset($this->data[$field]) && strlen($this->data[$field]) > $max) {
            $this->errors[$field] = $message ?? "The $field field must be at most $max characters";
        }
        return $this;
    }

    public function fails(): bool
    {
        return !empty($this->errors);
    }

    public function errors(): array
    {
        return $this->errors;
    }

    public function firstError(): ?string
    {
        return empty($this->errors) ? null : reset($this->errors);
    }
}