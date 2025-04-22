<?php

namespace App\Validation;

class FormValidation
{
    public function FormValidation(array $data, array $rules): array
    {
        $errors = [];

        foreach ($rules as $field => $ruleString) {
            $fieldRules = explode('|', $ruleString);
            $value = isset($data[$field]) ? trim((string) $data[$field]) : '';

            foreach ($fieldRules as $rule) {
                if ($rule === 'required') {
                    if ($value === '') {
                        $errors[$field] = "Пожалуйста, заполните поле $field.";
                        break;
                    }
                }

                if ($rule === 'email') {
                    if ($value !== '' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                        $errors[$field] = 'Некорректный email.';
                        break;
                    }
                }

                if (str_starts_with($rule, 'min:')) {
                    $minLength = (int)explode(':', $rule)[1];
                    if ($value !== '' && mb_strlen($value) < $minLength) {
                        $errors[$field] = "Поле $field должно быть не менее $minLength символов.";
                        break;
                    }
                }
            }
        }

        return $errors;
    }
}
