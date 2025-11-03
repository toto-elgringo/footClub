# FootClub

Application web de gestion de club de football.

## Technologies

- **PHP 8+** avec autoload Composer (PSR-4)
- **Twig 3.21** pour les templates
- **MySQL** avec PDO
- **Architecture OOP** (traits, enums, interfaces)

## Installation

```bash
composer install
```

Configurer la connexion MySQL dans `src/database/Database.php`

## Structure

```
src/
├── database/      # Connexion PDO
├── Helper/        # TwigRenderer, FormValidator, UploadPicture
└── Model/         # Classes, Manager, Enum, Trait

public/
├── pages/         # Points d'entrée PHP
├── templates/     # Templates Twig
└── uploads/       # Photos uploadées
```

## Fonctionnalités

- Gestion des joueurs (avec photos)
- Gestion des équipes
- Gestion du staff
- Gestion des matchs
- Gestion des clubs adverses
