<?php
namespace Helper;

class FormValidator
{
    public array $errors = [];

    public function required(array $fields, array $data): void
    {
        foreach ($fields as $field) {
            if (empty(trim($data[$field] ?? ''))) {
                $this->errors[$field] = "Le champ '$field' est obligatoire.";
            }
        }
    }

    public function hasErrors(): bool // verifie s'il y a des erreur de validation, true si erreur, falso si non
    {
        return !empty($this->errors);
    }

    public function getErrors(): array // retourne le tableau complet de serreurs de validation
    {
        return $this->errors;
    }
    
    public function addError(string $message): void
    {
        $this->errors[] = $message;
    }
}
