<?php
namespace App\Helper;

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

    public function hasErrors(): bool // verifie s'il y a des erreur de validation, true si erreur, false si non
    {
        return !empty($this->errors);
    }

    public function getErrors(): array // retourne le tableau complet de erreurs de validation
    {
        return $this->errors;
    }
    
    public function addError(string $message): void
    {
        $this->errors[] = $message;
    }

    public static function displayErrors(FormValidator $validator): void
    {
        if ($validator->hasErrors()) {
            echo '<div class="error">';
            foreach ($validator->getErrors() as $err) {
                echo '<div>' . htmlspecialchars($err) . '</div>';
            }
            echo '</div>';
        }
    }
}
