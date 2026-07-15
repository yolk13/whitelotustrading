<?php

class Validator
{
    private array $errors = [];

    public function validate(array $data, array $rules): bool
    {
        $this->errors = [];

        foreach ($rules as $field => $fieldRules) {
            $value = $data[$field] ?? '';
            $label = str_replace('_', ' ', ucfirst($field));

            foreach ($fieldRules as $rule => $param) {
                if (is_numeric($rule)) {
                    $rule = $param;
                    $param = null;
                }

                $methodName = 'rule' . ucfirst($rule);
                if (method_exists($this, $methodName)) {
                    $this->$methodName($field, $value, $param, $label);
                }
            }
        }

        return empty($this->errors);
    }

    public function errors(): array
    {
        return $this->errors;
    }

    public function error(string $field): ?string
    {
        return $this->errors[$field] ?? null;
    }

    public function hasError(string $field): bool
    {
        return isset($this->errors[$field]);
    }

    private function addError(string $field, string $message): void
    {
        $this->errors[$field] = $message;
    }

    private function ruleRequired(string $field, mixed $value, ?string $param, string $label): void
    {
        if ($value === '' || $value === null) {
            $this->addError($field, "{$label} is required");
        }
    }

    private function ruleEmail(string $field, mixed $value, ?string $param, string $label): void
    {
        if ($value !== '' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->addError($field, "{$label} must be a valid email address");
        }
    }

    private function ruleMin(string $field, mixed $value, ?string $param, string $label): void
    {
        if (is_string($value) && strlen($value) < (int)$param) {
            $this->addError($field, "{$label} must be at least {$param} characters");
        }
        if (is_numeric($value) && (float)$value < (float)$param) {
            $this->addError($field, "{$label} must be at least {$param}");
        }
    }

    private function ruleMax(string $field, mixed $value, ?string $param, string $label): void
    {
        if (is_string($value) && strlen($value) > (int)$param) {
            $this->addError($field, "{$label} must not exceed {$param} characters");
        }
        if (is_numeric($value) && (float)$value > (float)$param) {
            $this->addError($field, "{$label} must not exceed {$param}");
        }
    }

    private function ruleAlpha(string $field, mixed $value, ?string $param, string $label): void
    {
        if ($value !== '' && !ctype_alpha(str_replace(' ', '', $value))) {
            $this->addError($field, "{$label} may only contain letters");
        }
    }

    private function ruleAlphanumeric(string $field, mixed $value, ?string $param, string $label): void
    {
        if ($value !== '' && !preg_match('/^[a-zA-Z0-9\s\-]+$/', $value)) {
            $this->addError($field, "{$label} may only contain letters, numbers, spaces and hyphens");
        }
    }

    private function ruleNumeric(string $field, mixed $value, ?string $param, string $label): void
    {
        if ($value !== '' && !is_numeric($value)) {
            $this->addError($field, "{$label} must be a number");
        }
    }

    private function rulePhone(string $field, mixed $value, ?string $param, string $label): void
    {
        if ($value !== '' && !preg_match('/^[\+\d\s\-\(\)]{7,20}$/', $value)) {
            $this->addError($field, "{$label} must be a valid phone number");
        }
    }

    private function ruleIn(string $field, mixed $value, ?string $param, string $label): void
    {
        if ($value !== '') {
            $allowed = explode(',', $param);
            if (!in_array($value, $allowed)) {
                $this->addError($field, "{$label} has an invalid value");
            }
        }
    }

    private function ruleUrl(string $field, mixed $value, ?string $param, string $label): void
    {
        if ($value !== '' && !filter_var($value, FILTER_VALIDATE_URL)) {
            $this->addError($field, "{$label} must be a valid URL");
        }
    }

    private function ruleInteger(string $field, mixed $value, ?string $param, string $label): void
    {
        if ($value !== '' && !filter_var($value, FILTER_VALIDATE_INT)) {
            $this->addError($field, "{$label} must be a whole number");
        }
    }

    private function ruleJson(string $field, mixed $value, ?string $param, string $label): void
    {
        if ($value !== '') {
            json_decode($value);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $this->addError($field, "{$label} must be valid JSON");
            }
        }
    }
}
