# Migration vers Twig - Documentation

## Vue d'ensemble

Votre projet FootClub a été entièrement migré pour utiliser **Twig** comme moteur de template. Cette migration sépare clairement la logique métier (PHP) de la présentation (HTML), rendant le code plus maintenable et plus facile à comprendre.

## Structure du projet après migration

```
footClub/
├── public/
│   ├── pages/                      # Fichiers PHP (logique métier uniquement)
│   │   ├── index.php
│   │   ├── joueurs.php
│   │   ├── joueursUpdate.php
│   │   ├── equipes.php
│   │   ├── equipesUpdate.php
│   │   ├── matchs.php
│   │   ├── matchsUpdate.php
│   │   ├── staff.php
│   │   └── staffUpdate.php
│   └── templates/                  # Templates Twig (présentation uniquement)
│       ├── layouts/
│       │   └── base.twig          # Template de base (structure HTML commune)
│       ├── components/
│       │   ├── navbar.twig        # Composant de navigation
│       │   ├── footer.twig        # Composant de pied de page
│       │   └── errors.twig        # Composant d'affichage des erreurs
│       └── pages/
│           ├── index.twig
│           ├── joueurs.twig
│           ├── joueursUpdate.twig
│           ├── equipes.twig
│           ├── equipesUpdate.twig
│           ├── matchs.twig
│           ├── matchsUpdate.twig
│           ├── staff.twig
│           └── staffUpdate.twig
├── src/
│   └── Helper/
│       └── TwigRenderer.php       # Classe de gestion de Twig
└── var/
    └── cache/
        └── twig/                   # Cache des templates compilés
```

## Nouveaux fichiers créés

### 1. TwigRenderer.php (src/Helper/TwigRenderer.php)

Cette classe est le cœur de l'intégration Twig. Elle permet de :
- Configurer Twig avec le bon chemin vers les templates
- Gérer le cache des templates
- Rendre les templates avec des données

**Utilisation :**
```php
use App\Helper\TwigRenderer;

// Afficher un template avec des données
TwigRenderer::display('pages/index.twig', [
    'players' => $players,
    'teams' => $teams
]);
```

### 2. Templates Twig

#### Template de base (layouts/base.twig)
- Définit la structure HTML commune à toutes les pages
- Inclut automatiquement la navbar et le footer
- Permet aux pages enfants de surcharger certaines parties (titre, CSS, contenu, scripts)

#### Composants (components/)
- **navbar.twig** : Barre de navigation réutilisable
- **footer.twig** : Pied de page réutilisable
- **errors.twig** : Affichage des erreurs de validation

#### Pages (pages/)
Chaque page étend le template de base et définit son contenu spécifique.

## Changements dans les fichiers PHP

### Avant (exemple avec index.php)
```php
<?php
include "../includes/navbar.php";
$players = $playerManager->findAll();
?>
<!DOCTYPE html>
<html>
<head>...</head>
<body>
    <h1>Tableau de bord</h1>
    <p><?php echo count($players); ?> joueurs</p>
</body>
</html>
```

### Après
```php
<?php
require_once __DIR__ . '/../../vendor/autoload.php';
use App\Helper\TwigRenderer;
use App\Model\Manager\PlayerManager;

$playerManager = new PlayerManager();
$players = $playerManager->findAll();

// Tout le HTML est dans le template Twig
TwigRenderer::display('pages/index.twig', [
    'players' => $players
]);
```

## Syntaxe Twig - Comprendre les templates

### 1. Afficher une variable
```twig
{# Ancien PHP: <?php echo $player->getName(); ?> #}
{{ player.getName() }}

{# Avec échappement automatique des caractères spéciaux #}
{{ player.getName()|e }}
```

### 2. Structures de contrôle

**Boucles :**
```twig
{# Ancien PHP: foreach($players as $player) { ... } #}
{% for player in players %}
    <h2>{{ player.getFirstname() }} {{ player.getLastname() }}</h2>
{% endfor %}
```

**Conditions :**
```twig
{# Ancien PHP: if (!empty($teams)) { ... } #}
{% if teams is not empty %}
    <p>Il y a {{ teams|length }} équipes</p>
{% else %}
    <p>Aucune équipe</p>
{% endif %}
```

### 3. Héritage de templates

**Template enfant étend le template de base :**
```twig
{% extends 'layouts/base.twig' %}

{% block title %}Joueurs - Foot Club{% endblock %}

{% block content %}
    <h1>Liste des joueurs</h1>
    {# Contenu de la page #}
{% endblock %}
```

### 4. Inclusion de composants
```twig
{# Inclure la navbar #}
{% include 'components/navbar.twig' %}

{# Inclure avec des variables spécifiques #}
{% include 'components/errors.twig' with {'validator': validator} %}
```

### 5. Filtres Twig utiles

```twig
{# Échapper les caractères HTML #}
{{ player.getName()|e }}

{# Compter des éléments #}
{{ players|length }}

{# Formater une date #}
{{ match.getDate().format('d/m/Y') }}

{# Concaténer des chaînes #}
{{ player.getFirstname() ~ ' ' ~ player.getLastname() }}
```

### 6. Commentaires
```twig
{# Ceci est un commentaire Twig (non visible dans le HTML) #}
```

## Avantages de la migration

### 1. Séparation des préoccupations
- **PHP** : Gère uniquement la logique métier (récupération de données, validation, etc.)
- **Twig** : Gère uniquement la présentation (affichage HTML)

### 2. Code plus lisible
```php
// Avant (mélange PHP et HTML)
<h2><?php echo htmlspecialchars($player->getFirstname() . ' ' . $player->getLastname()); ?></h2>

// Après (Twig propre)
<h2>{{ player.getFirstname() }} {{ player.getLastname() }}</h2>
```

### 3. Sécurité renforcée
- Twig échappe automatiquement les variables par défaut
- Protection contre les failles XSS

### 4. Réutilisabilité
- Les composants (navbar, footer, errors) ne sont définis qu'une seule fois
- Le template de base évite la duplication du code HTML

### 5. Maintenabilité
- Modification du design : toucher uniquement les fichiers .twig
- Modification de la logique : toucher uniquement les fichiers .php

## Exemple complet : Page des joueurs

### Fichier PHP (public/pages/joueurs.php)
```php
<?php
// Charge les dépendances
require_once __DIR__ . '/../../vendor/autoload.php';

// Import des classes
use App\Model\Classes\Player;
use App\Helper\TwigRenderer;
use App\Model\Manager\PlayerManager;
// ... autres imports

// Initialisation
$playerManager = new PlayerManager();
$players = $playerManager->findAll();
$validator = new FormValidator();

// Traitement des formulaires (ajout, suppression, etc.)
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Logique de traitement...
}

// Rendu du template avec les données
TwigRenderer::display('pages/joueurs.twig', [
    'players' => $players,
    'teams' => $teams,
    'validator' => $validator,
    'playerManager' => $playerManager
]);
```

### Template Twig (public/templates/pages/joueurs.twig)
```twig
{# Hérite du template de base #}
{% extends 'layouts/base.twig' %}

{# Définit le titre de la page #}
{% block title %}Joueurs - Foot Club{% endblock %}

{# Ajoute des CSS spécifiques #}
{% block stylesheets %}
    <link rel="stylesheet" href="../css/joueurs.css">
{% endblock %}

{# Définit le contenu principal #}
{% block content %}
    <div class="header">
        <h1>Joueurs</h1>

        {# Affiche les erreurs si présentes #}
        {% include 'components/errors.twig' with {'validator': validator} %}

        {# Formulaire d'ajout #}
        <form action="joueurs.php" method="post">
            {# Champs du formulaire #}
        </form>
    </div>

    {# Liste des joueurs #}
    <div class="dashboard">
        {% for player in players %}
            <div class="player-card">
                <h2>{{ player.getFirstname() }} {{ player.getLastname() }}</h2>
                <p>{{ playerManager.getAge(player) }} ans</p>
            </div>
        {% endfor %}
    </div>
{% endblock %}

{# Ajoute des scripts spécifiques #}
{% block scripts %}
    <script src="../js/joueurs.js"></script>
{% endblock %}
```

## Points importants

### 1. Le style et la structure front-end sont préservés
- Tous les classes CSS sont identiques
- La structure HTML est la même
- Les fichiers JavaScript sont toujours inclus

### 2. Appel des méthodes PHP dans Twig
```twig
{# Les méthodes PHP s'appellent normalement #}
{{ player.getFirstname() }}
{{ team.getId() }}
{{ match.getDate().format('Y-m-d') }}
```

### 3. Gestion des enum
```twig
{# Pour afficher la valeur d'un enum #}
{{ role.value }}

{# Exemple avec StaffRole #}
{{ staff.getRole().value }}
```

### 4. Cache Twig
- Les templates sont compilés et mis en cache dans `var/cache/twig/`
- En développement, le cache se recharge automatiquement (auto_reload: true)
- En production, désactiver auto_reload et debug pour de meilleures performances

## Commandes utiles

### Vider le cache Twig
```bash
# Windows
rmdir /s /q var\cache\twig

# Linux/Mac
rm -rf var/cache/twig
```

## Résumé de la migration

✅ **13 pages migrées vers Twig :**
- index.php / index.twig
- joueurs.php / joueurs.twig
- joueursUpdate.php / joueursUpdate.twig
- equipes.php / equipes.twig
- equipesUpdate.php / equipesUpdate.twig
- matchs.php / matchs.twig
- matchsUpdate.php / matchsUpdate.twig
- staff.php / staff.twig
- staffUpdate.php / staffUpdate.twig

✅ **Composants créés :**
- navbar.twig (remplace navbar.php)
- footer.twig (remplace footer.php)
- errors.twig (affichage des erreurs)

✅ **Template de base créé :**
- layouts/base.twig (structure HTML commune)

✅ **Classe helper créée :**
- TwigRenderer.php (gestion de Twig)

## Le code est entièrement commenté

Chaque fichier contient des commentaires détaillés en français qui expliquent :
- Ce que fait chaque section
- Les variables attendues
- Les traitements effectués
- La syntaxe Twig utilisée

Vous pouvez maintenant comprendre et maintenir facilement votre code !
