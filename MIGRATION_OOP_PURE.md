# Migration vers une Architecture OOP Pure (Sans IDs exposés)

## 📖 Table des matières
1. [Principe fondamental](#principe-fondamental)
2. [Pourquoi cette migration ?](#pourquoi-cette-migration)
3. [Étapes de migration détaillées](#étapes-de-migration-détaillées)
4. [Exemples concrets avant/après](#exemples-concrets-avantaprès)
5. [Défis rencontrés et solutions](#défis-rencontrés-et-solutions)
6. [Contraintes et limitations](#contraintes-et-limitations)
7. [Guide d'utilisation](#guide-dutilisation)

---

## 🎯 Principe fondamental

### Concept de l'OOP pure
Dans une architecture OOP (Programmation Orientée Objet) pure, **les objets du domaine métier ne doivent pas exposer les détails d'implémentation de la persistance**.

#### Avant (Architecture couplée à la BDD)
```php
class Player {
    private ?int $id;  // ❌ Détail d'implémentation de la BDD exposé
    private string $firstname;
    private string $lastname;
}

$player->getId();  // ❌ Le métier manipule des IDs techniques
```

#### Après (Architecture OOP pure)
```php
class Player {
    // ✅ Plus d'ID : l'objet ne sait pas qu'il vient d'une BDD
    private string $firstname;
    private string $lastname;
}

$player->getFirstname();  // ✅ Le métier manipule des concepts métier
```

### Les IDs : détails techniques vs identifiants métier

**IDs techniques** (auto-increment en BDD) :
- Générés par la base de données
- N'ont aucune signification métier
- Simples compteurs techniques
- ❌ Ne devraient pas être exposés dans le domaine métier

**Identifiants métier** (natural keys) :
- Ont une signification métier (nom, prénom, date)
- Sont stables et uniques par nature
- ✅ Peuvent être utilisés pour identifier les entités

---

## 💡 Pourquoi cette migration ?

### Problèmes de l'ancienne architecture

1. **Couplage fort avec la BDD**
```php
// ❌ Le code métier est couplé à la structure de la BDD
$match = new FootballMatch(null, $date, $city, $score1, $score2, $teamId, $clubId);
//                          ^^ null car l'ID sera généré par la BDD
//                                                              ^^^^^ ^^^^^^ IDs techniques
```

2. **Relations exprimées par des IDs**
```php
// ❌ Pour connaître l'équipe d'un match, il faut résoudre l'ID
$teamId = $match->getTeamId();
$team = $teamManager->findById($teamId);
$teamName = $team->getName();  // 3 étapes !
```

3. **Violation du principe d'encapsulation**
```php
// ❌ L'objet métier expose un détail d'implémentation
if ($player->getId() === $otherPlayer->getId()) {
    // Comparaison basée sur un détail technique
}
```

### Avantages de la nouvelle architecture

1. **Découplage domaine/persistance**
```php
// ✅ Le code métier manipule des objets métier
$match = new FootballMatch($date, $city, $score1, $score2, $team, $opposingClub);
//                                                           ^^^^^ ^^^^^^^^^^^^^ Objets métier
```

2. **Relations exprimées naturellement**
```php
// ✅ Accès direct aux propriétés métier
$teamName = $match->getTeam()->getName();  // 1 seule étape !
```

3. **Code plus expressif et lisible**
```php
// ✅ Comparaison basée sur des attributs métier
if ($player->getFirstname() === $otherPlayer->getFirstname() &&
    $player->getLastname() === $otherPlayer->getLastname()) {
    // Comparaison métier claire
}
```

---

## 🔧 Étapes de migration détaillées

### ÉTAPE 1 : Modification des classes modèle (src/Model/Classes/)

#### 1.1 Suppression des IDs dans les classes de base

**Fichier : `Person.php` (classe abstraite)**

**AVANT :**
```php
abstract class Person {
    public function __construct(
        private ?int $id,           // ❌ À supprimer
        private string $firstname,
        private string $lastname,
        private string $picture
    ) {}

    public function getId(): ?int { return $this->id; }          // ❌ À supprimer
    public function setId(int $id): void { $this->id = $id; }    // ❌ À supprimer
}
```

**APRÈS :**
```php
abstract class Person {
    public function __construct(
        private string $firstname,   // ✅ Seulement des attributs métier
        private string $lastname,
        private string $picture
    ) {}
    // ✅ Plus de getId() ni setId()
}
```

**Ce qui a été fait :**
1. ✅ Supprimé la propriété `private ?int $id`
2. ✅ Supprimé le paramètre `$id` du constructeur
3. ✅ Supprimé les méthodes `getId()` et `setId()`

#### 1.2 Adaptation des classes enfants (Player, StaffMember)

**Fichier : `Player.php`**

**AVANT :**
```php
class Player extends Person {
    public function __construct(
        private ?int $id,           // ❌ À supprimer
        private string $firstname,  // ❌ Redéfini (déjà dans Person)
        private string $lastname,   // ❌ Redéfini (déjà dans Person)
        private DateTime $birthdate,
        private string $picture     // ❌ Redéfini (déjà dans Person)
    ) {}

    // ❌ Toutes les méthodes redéfinies (déjà dans Person)
    public function getFirstname(): string { return $this->firstname; }
    public function getLastname(): string { return $this->lastname; }
    // ...
}
```

**APRÈS :**
```php
class Player extends Person {
    public function __construct(
        string $firstname,           // ✅ Passé au parent
        string $lastname,            // ✅ Passé au parent
        private DateTime $birthdate, // ✅ Spécifique à Player
        string $picture              // ✅ Passé au parent
    ) {
        parent::__construct($firstname, $lastname, $picture); // ✅ Appel parent
    }

    // ✅ Seulement les méthodes spécifiques à Player
    public function getBirthdate(): DateTime { return $this->birthdate; }
    public function setBirthdate(string $birthdate): void {
        $this->birthdate = new DateTime($birthdate);
    }
}
```

**Ce qui a été fait :**
1. ✅ Supprimé le paramètre `$id`
2. ✅ Supprimé les propriétés déjà définies dans `Person` (firstname, lastname, picture)
3. ✅ Ajouté l'appel `parent::__construct()` pour initialiser les propriétés héritées
4. ✅ Supprimé les méthodes déjà définies dans `Person` (getFirstname, getLastname, etc.)
5. ✅ Gardé seulement les méthodes spécifiques (getBirthdate, setBirthdate)

**Même processus pour `StaffMember.php`**

#### 1.3 Suppression des IDs dans les classes simples

**Fichier : `Team.php`**

**AVANT :**
```php
class Team {
    public function __construct(
        private ?int $id,      // ❌ À supprimer
        private string $name
    ) {}

    public function getId(): ?int { return $this->id; }          // ❌ À supprimer
    public function setId(int $id): void { $this->id = $id; }    // ❌ À supprimer
}
```

**APRÈS :**
```php
class Team {
    public function __construct(private string $name) {}  // ✅ Seulement le nom

    public function getName(): string { return $this->name; }
    public function setName(string $name): void { $this->name = $name; }
}
```

**Ce qui a été fait :**
1. ✅ Supprimé `private ?int $id`
2. ✅ Supprimé `getId()` et `setId()`
3. ✅ Le nom devient l'identifiant naturel unique

**Même processus pour :**
- `OpposingClub.php` : identifié par `$name` + `$city`
- `FootballMatch.php` : identifié par `$date` + `$city`

#### 1.4 Remplacement des IDs par des objets dans les relations

**Fichier : `PlayerTeam.php`**

**AVANT :**
```php
class PlayerTeam {
    public function __construct(
        private int $playerId,    // ❌ ID technique
        private int $teamId,      // ❌ ID technique
        private PlayerRole $role
    ) {}

    public function getPlayerId(): int { return $this->playerId; }
    public function getTeamId(): int { return $this->teamId; }
}
```

**APRÈS :**
```php
class PlayerTeam {
    public function __construct(
        private Player $player,   // ✅ Objet complet
        private Team $team,       // ✅ Objet complet
        private PlayerRole $role
    ) {}

    public function getPlayer(): Player { return $this->player; }
    public function getTeam(): Team { return $this->team; }
}
```

**Ce qui a été fait :**
1. ✅ Remplacé `int $playerId` par `Player $player`
2. ✅ Remplacé `int $teamId` par `Team $team`
3. ✅ Remplacé `getPlayerId()` par `getPlayer()`
4. ✅ Remplacé `getTeamId()` par `getTeam()`
5. ✅ Les objets contiennent maintenant toutes leurs données

**Même processus pour `FootballMatch.php` :**
- `private ?int $teamId` → `private ?Team $team`
- `private int $opposingClubId` → `private OpposingClub $opposingClub`

---

### ÉTAPE 2 : Refonte des Managers (src/Model/Manager/)

Les Managers doivent maintenant faire le pont entre le monde métier (objets sans ID) et la persistance (BDD avec IDs).

#### 2.1 Remplacement de findById() par des méthodes métier

**Fichier : `PlayerManager.php`**

**AVANT :**
```php
public function findById(int $id): ?Player {
    $stmt = $this->db->prepare("SELECT * FROM player WHERE id = ?");
    $stmt->execute([$id]);
    $row = $stmt->fetch();

    if ($row) {
        return new Player(
            $row['id'],           // ❌ Passe l'ID
            $row['firstname'],
            $row['lastname'],
            new DateTime($row['birthdate']),
            $row['picture']
        );
    }
    return null;
}
```

**APRÈS :**
```php
public function findByName(string $firstname, string $lastname): ?Player {
    $stmt = $this->db->prepare("SELECT * FROM player WHERE firstname = ? AND lastname = ?");
    $stmt->execute([$firstname, $lastname]);
    $row = $stmt->fetch();

    if ($row) {
        return new Player(
            // ✅ Plus d'ID passé au constructeur
            $row['firstname'],
            $row['lastname'],
            new DateTime($row['birthdate']),
            $row['picture']
        );
    }
    return null;
}
```

**Ce qui a été fait :**
1. ✅ Renommé `findById(int $id)` → `findByName(string $firstname, string $lastname)`
2. ✅ Modifié la clause WHERE : `id = ?` → `firstname = ? AND lastname = ?`
3. ✅ Retiré `$row['id']` du constructeur de Player
4. ✅ Utilisé des identifiants métier (firstname + lastname) au lieu d'un ID technique

**Même processus pour :**
- `TeamManager` : `findById()` → `findByName(string $name)`
- `StaffMemberManager` : `findById()` → `findByName(string $firstname, string $lastname)`
- `MatchManager` : `findById()` → `findByDateAndCity(string $date, string $city)`
- `OpposingClubManager` : `findById()` → `findByCity()` et `findByName()`

#### 2.2 Adaptation des méthodes delete()

**AVANT :**
```php
public function delete(object $object): bool {
    $this->checkInstanceOf($object, Player::class);

    $stmt = $this->db->prepare("DELETE FROM player WHERE id = ?");
    return $stmt->execute([$object->getId()]);  // ❌ Utilise l'ID
}
```

**APRÈS :**
```php
public function delete(object $object): bool {
    $this->checkInstanceOf($object, Player::class);

    // ✅ Utilise les attributs métier pour identifier l'enregistrement
    $stmt = $this->db->prepare("DELETE FROM player WHERE firstname = ? AND lastname = ?");
    return $stmt->execute([
        $object->getFirstname(),
        $object->getLastname()
    ]);
}
```

**Ce qui a été fait :**
1. ✅ Modifié la clause WHERE pour utiliser firstname + lastname
2. ✅ Remplacé `$object->getId()` par `$object->getFirstname()` et `$object->getLastname()`

#### 2.3 Adaptation des méthodes update()

**AVANT :**
```php
public function update(object $object): bool {
    $this->checkInstanceOf($object, Player::class);

    $stmt = $this->db->prepare(
        "UPDATE player SET firstname = ?, lastname = ?, birthdate = ?, picture = ? WHERE id = ?"
    );
    return $stmt->execute([
        $object->getFirstname(),
        $object->getLastname(),
        $object->getBirthdate()->format("Y-m-d"),
        $object->getPicture(),
        $object->getId()  // ❌ Utilise l'ID
    ]);
}
```

**APRÈS :**
```php
public function update(object $object): bool {
    $this->checkInstanceOf($object, Player::class);

    // ✅ Met à jour birthdate et picture, identifie par firstname + lastname
    $stmt = $this->db->prepare(
        "UPDATE player SET birthdate = ?, picture = ? WHERE firstname = ? AND lastname = ?"
    );
    return $stmt->execute([
        $object->getBirthdate()->format("Y-m-d"),
        $object->getPicture(),
        $object->getFirstname(),  // ✅ Identifiant métier
        $object->getLastname()    // ✅ Identifiant métier
    ]);
}
```

**Ce qui a été fait :**
1. ✅ Retiré firstname et lastname du SET (ce sont les identifiants, on ne peut pas les modifier simplement)
2. ✅ Utilisé firstname + lastname dans le WHERE au lieu de l'ID
3. ✅ **Problème** : on ne peut plus renommer un joueur avec un simple UPDATE

**Pour TeamManager, problème similaire : besoin de l'ancien nom**

**Solution :**
```php
public function update(object $object, string $oldName): bool {
    $this->checkInstanceOf($object, Team::class);

    // ✅ Utilise l'ancien nom pour identifier, le nouveau pour mettre à jour
    $stmt = $this->db->prepare("UPDATE team SET name = ? WHERE name = ?");
    return $stmt->execute([
        $object->getName(),  // Nouveau nom
        $oldName             // Ancien nom (pour WHERE)
    ]);
}
```

#### 2.4 Hydratation complète dans PlayerTeamManager

**AVANT :**
```php
public function findAll(): array {
    $sql = "SELECT pht.*, t.name as team_name
            FROM player_has_team pht
            JOIN team t ON pht.team_id = t.id";

    $stmt = $this->db->query($sql);
    $playerTeams = [];

    while ($data = $stmt->fetch()) {
        $playerTeams[] = [
            "playerTeam" => new PlayerTeam(
                $data['player_id'],  // ❌ ID
                $data['team_id'],    // ❌ ID
                PlayerRole::from($data['role'])
            ),
            "team_name" => $data['team_name']
        ];
    }
    return $playerTeams;
}
```

**APRÈS :**
```php
public function findAll(): array {
    // ✅ Join pour récupérer toutes les données nécessaires
    $sql = "SELECT p.firstname, p.lastname, p.birthdate, p.picture,
                   t.name as team_name, pht.role
            FROM player_has_team pht
            JOIN player p ON pht.player_id = p.id
            JOIN team t ON pht.team_id = t.id";

    $stmt = $this->db->query($sql);
    $playerTeams = [];

    while ($data = $stmt->fetch()) {
        // ✅ Créer les objets complets
        $player = new Player(
            $data['firstname'],
            $data['lastname'],
            new DateTime($data['birthdate']),
            $data['picture']
        );
        $team = new Team($data['team_name']);

        $playerTeams[] = [
            "playerTeam" => new PlayerTeam(
                $player,  // ✅ Objet Player complet
                $team,    // ✅ Objet Team complet
                PlayerRole::from($data['role'])
            ),
            "team_name" => $data['team_name']
        ];
    }
    return $playerTeams;
}
```

**Ce qui a été fait :**
1. ✅ Modifié la requête SQL pour joindre les tables player et team
2. ✅ Récupéré toutes les colonnes nécessaires (firstname, lastname, birthdate, picture)
3. ✅ Créé des objets Player et Team complets
4. ✅ Passé les objets au constructeur de PlayerTeam au lieu d'IDs

**Même principe pour MatchManager :**

**AVANT :**
```php
while ($data = $stmt->fetch()) {
    $matches[] = new FootballMatch(
        $data['id'],
        new DateTime($data['date']),
        $data['city'],
        $data['team_score'],
        $data['opponent_score'],
        $data['team_id'],           // ❌ ID
        $data['opposing_club_id']   // ❌ ID
    );
}
```

**APRÈS :**
```php
// ✅ Requête avec JOINs
$query = "SELECT m.*, t.name as team_name, oc.address as club_name, oc.city as club_city
          FROM `match` m
          LEFT JOIN team t ON m.team_id = t.id
          JOIN opposing_club oc ON m.opposing_club_id = oc.id";

while ($data = $stmt->fetch()) {
    // ✅ Créer les objets complets
    $team = $data['team_name'] ? new Team($data['team_name']) : null;
    $opposingClub = new OpposingClub($data['club_name'], $data['club_city']);

    $matches[] = new FootballMatch(
        new DateTime($data['date']),
        $data['city'],
        $data['team_score'],
        $data['opponent_score'],
        $team,          // ✅ Objet Team
        $opposingClub   // ✅ Objet OpposingClub
    );
}
```

#### 2.5 Résolution d'IDs dans insert() et update()

**Problème** : La BDD a toujours des IDs et des clés étrangères. Il faut résoudre les objets en IDs avant l'insertion.

**Fichier : `PlayerTeamManager::insert()`**

**APRÈS :**
```php
public function insert(object $object): bool {
    $this->checkInstanceOf($object, PlayerTeam::class);

    // ✅ Résoudre le Player en player_id
    $stmtPlayer = $this->db->prepare("SELECT id FROM player WHERE firstname = ? AND lastname = ?");
    $stmtPlayer->execute([
        $object->getPlayer()->getFirstname(),
        $object->getPlayer()->getLastname()
    ]);
    $playerId = $stmtPlayer->fetchColumn();

    // ✅ Résoudre le Team en team_id
    $stmtTeam = $this->db->prepare("SELECT id FROM team WHERE name = ?");
    $stmtTeam->execute([$object->getTeam()->getName()]);
    $teamId = $stmtTeam->fetchColumn();

    // ✅ Insérer avec les IDs techniques
    $stmt = $this->db->prepare("INSERT INTO player_has_team (player_id, team_id, role) VALUES (?, ?, ?)");
    return $stmt->execute([
        $playerId,
        $teamId,
        $object->getRole()->value
    ]);
}
```

**Ce qui a été fait :**
1. ✅ Ajouté une requête SELECT pour trouver l'ID du player via firstname + lastname
2. ✅ Ajouté une requête SELECT pour trouver l'ID de la team via name
3. ✅ Utilisé ces IDs pour l'INSERT dans la table de jointure
4. ✅ Le code métier manipule des objets, le Manager fait la conversion vers IDs

**Même principe pour `MatchManager::insert()` et toutes les méthodes manipulant des relations**

#### 2.6 Modification de l'interface ManagerInterface

**AVANT :**
```php
interface ManagerInterface {
    public function findAll(): array;
    public function findById(int $id): ?object;  // ❌ Chaque Manager a sa propre méthode
    public function insert(object $object): bool;
    public function delete(object $object): bool;
    public function update(object $object): bool;  // ❌ Signatures différentes
}
```

**APRÈS :**
```php
interface ManagerInterface {
    public function findAll(): array;        // ✅ Signature commune
    public function insert(object $object): bool;  // ✅ Signature commune
    public function delete(object $object): bool;  // ✅ Signature commune
    // ✅ Plus de findById() : chaque Manager a sa propre méthode
    // ✅ Plus de update() : signatures différentes selon les Managers
}
```

**Ce qui a été fait :**
1. ✅ Supprimé `findById()` car chaque Manager utilise des identifiants différents
2. ✅ Supprimé `update()` car certains Managers ont besoin de paramètres supplémentaires (oldName, oldDate, etc.)
3. ✅ Gardé seulement les 3 méthodes avec signatures communes

---

### ÉTAPE 3 : Adaptation des pages PHP (public/pages/)

#### 3.1 Modification de la suppression

**Fichier : `joueurs.php`**

**AVANT :**
```php
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['action']) && $_POST['action'] === 'delete' && isset($_POST['id'])) {
    $id = (int) $_POST['id'];
    $playerToDelete = $playerManager->findById($id);  // ❌ Recherche par ID

    if ($playerToDelete instanceof Player) {
        UploadPicture::delete($playerToDelete->getPicture());

        if ($playerManager->delete($playerToDelete)) {
            Redirect::to("joueurs.php");
        } else {
            $validator->addError("La suppression a échoué.");
        }
    } else {
        $validator->addError("Joueur introuvable.");
    }
}
```

**APRÈS :**
```php
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['action']) && $_POST['action'] === 'delete' && isset($_POST['firstname'], $_POST['lastname'])) {
    $firstname = trim($_POST['firstname']);     // ✅ Identifiants métier
    $lastname = trim($_POST['lastname']);       // ✅ Identifiants métier
    $playerToDelete = $playerManager->findByName($firstname, $lastname);  // ✅ Recherche métier

    if ($playerToDelete instanceof Player) {
        UploadPicture::delete($playerToDelete->getPicture());

        if ($playerManager->delete($playerToDelete)) {
            Redirect::to("joueurs.php");
        } else {
            $validator->addError("La suppression a échoué.");
        }
    } else {
        $validator->addError("Joueur introuvable.");
    }
}
```

**Ce qui a été fait :**
1. ✅ Changé `isset($_POST['id'])` → `isset($_POST['firstname'], $_POST['lastname'])`
2. ✅ Récupéré firstname et lastname au lieu de l'ID
3. ✅ Utilisé `findByName()` au lieu de `findById()`
4. ✅ Le reste du code reste identique

**Même processus pour toutes les pages de suppression**

#### 3.2 Modification de la création

**Fichier : `joueurs.php`**

**AVANT :**
```php
$player = new Player(
    null,                    // ❌ ID null (sera auto-généré)
    $prenom,
    $nom,
    new DateTime($birthdate),
    $uploadResult['filename']
);
```

**APRÈS :**
```php
$player = new Player(
    // ✅ Plus d'ID
    $prenom,
    $nom,
    new DateTime($birthdate),
    $uploadResult['filename']
);
```

**Ce qui a été fait :**
1. ✅ Retiré le premier paramètre `null` (l'ID)
2. ✅ L'objet ne sait pas qu'il sera persisté en BDD

#### 3.3 Modification de la mise à jour

**Fichier : `joueursUpdate.php`**

**AVANT :**
```php
// Récupération du joueur
$player = $playerManager->findById($_GET['id']);

// Formulaire de mise à jour
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_player'])) {
    $prenom = trim($_POST['prenom'] ?? '');
    $nom = trim($_POST['nom'] ?? '');
    $birthdate = trim($_POST['birthdate'] ?? '');

    // ...gestion upload...

    $updated = new Player(
        $player->getId(),     // ❌ Réutilise l'ID
        $prenom,
        $nom,
        new DateTime($birthdate),
        $newPicture
    );

    if ($playerManager->update($updated)) {
        Redirect::to("joueurs.php");
    }
}
```

**APRÈS :**
```php
// ✅ Récupération via firstname + lastname
$player = isset($_GET['firstname'], $_GET['lastname'])
    ? $playerManager->findByName($_GET['firstname'], $_GET['lastname'])
    : null;

// Formulaire de mise à jour
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_player'])) {
    $old_firstname = trim($_POST['old_firstname'] ?? '');  // ✅ Anciens identifiants
    $old_lastname = trim($_POST['old_lastname'] ?? '');
    $prenom = trim($_POST['prenom'] ?? '');
    $nom = trim($_POST['nom'] ?? '');
    $birthdate = trim($_POST['birthdate'] ?? '');

    // ...gestion upload...

    // ✅ Logique spéciale si les noms changent
    if ($old_firstname !== $prenom || $old_lastname !== $nom) {
        // DELETE l'ancien
        $oldPlayer = new Player($old_firstname, $old_lastname, $player->getBirthdate(), $player->getPicture());
        $playerManager->delete($oldPlayer);

        // INSERT le nouveau
        $newPlayer = new Player($prenom, $nom, new DateTime($birthdate), $newPicture);
        if ($playerManager->insert($newPlayer)) {
            Redirect::to("joueurs.php");
        }
    } else {
        // ✅ UPDATE simple (juste birthdate et picture)
        $updated = new Player($prenom, $nom, new DateTime($birthdate), $newPicture);
        if ($playerManager->update($updated)) {
            Redirect::to("joueurs.php");
        }
    }
}
```

**Ce qui a été fait :**
1. ✅ Changé `$_GET['id']` → `$_GET['firstname']` et `$_GET['lastname']`
2. ✅ Ajouté des champs hidden `old_firstname` et `old_lastname` dans le formulaire
3. ✅ Ajouté une logique spéciale : si les noms changent, on fait DELETE + INSERT
4. ✅ Sinon, simple UPDATE de birthdate et picture
5. ✅ **Limitation** : les relations PlayerTeam seront perdues si on change le nom

#### 3.4 Modification des formulaires de relation

**Fichier : `joueurs.php` (ajout d'un joueur à une équipe)**

**AVANT :**
```php
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['player_id'], $_POST['team_id'], $_POST['role'])) {
    $player_id = trim($_POST['player_id']);     // ❌ ID
    $team_id = trim($_POST['team_id']);         // ❌ ID
    $roleStr = trim($_POST['role']);

    $role = PlayerRole::from($roleStr);

    if ($playerTeamManager->exists($player_id, $team_id)) {
        $validator->addError("Le joueur appartient déjà à l'équipe");
    } else {
        $playerTeam = new PlayerTeam($player_id, $team_id, $role);  // ❌ IDs

        if ($playerTeamManager->insert($playerTeam)) {
            Redirect::to("joueurs.php");
        }
    }
}
```

**APRÈS :**
```php
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['player_firstname'], $_POST['player_lastname'], $_POST['team_name'], $_POST['role'])) {
    $player_firstname = trim($_POST['player_firstname']);  // ✅ Identifiants métier
    $player_lastname = trim($_POST['player_lastname']);
    $team_name = trim($_POST['team_name']);
    $roleStr = trim($_POST['role']);

    $role = PlayerRole::from($roleStr);

    if ($playerTeamManager->exists($player_firstname, $player_lastname, $team_name)) {
        $validator->addError("Le joueur appartient déjà à l'équipe");
    } else {
        // ✅ Charger les objets complets
        $player = $playerManager->findByName($player_firstname, $player_lastname);
        $team = $teamManager->findByName($team_name);

        if ($player && $team) {
            $playerTeam = new PlayerTeam($player, $team, $role);  // ✅ Objets

            if ($playerTeamManager->insert($playerTeam)) {
                Redirect::to("joueurs.php");
            }
        } else {
            $validator->addError("Joueur ou équipe introuvable");
        }
    }
}
```

**Ce qui a été fait :**
1. ✅ Changé les noms de champs : `player_id`/`team_id` → `player_firstname`/`player_lastname`/`team_name`
2. ✅ Utilisé `findByName()` pour charger les objets Player et Team complets
3. ✅ Passé les objets au constructeur de PlayerTeam
4. ✅ Le PlayerTeamManager résoudra les IDs en interne

---

### ÉTAPE 4 : Modification des templates Twig (public/templates/pages/)

#### 4.1 Formulaires de suppression

**Fichier : `joueurs.twig`**

**AVANT :**
```twig
<div class="player-card card" data-type="player" data-id="{{ player.getId() }}">
    <span class="delete">✕</span>
    <form method="post" action="joueurs.php" class="delete-player-form delete-form" style="display:none;">
        <input type="hidden" name="action" value="delete">
        <input type="hidden" name="id" value="{{ player.getId() }}">
    </form>
```

**APRÈS :**
```twig
<div class="player-card card" data-type="player" data-firstname="{{ player.getFirstname() }}" data-lastname="{{ player.getLastname() }}">
    <span class="delete">✕</span>
    <form method="post" action="joueurs.php" class="delete-player-form delete-form" style="display:none;">
        <input type="hidden" name="action" value="delete">
        <input type="hidden" name="firstname" value="{{ player.getFirstname() }}">
        <input type="hidden" name="lastname" value="{{ player.getLastname() }}">
    </form>
```

**Ce qui a été fait :**
1. ✅ Changé `data-id` → `data-firstname` et `data-lastname`
2. ✅ Changé `name="id"` → `name="firstname"` et `name="lastname"`
3. ✅ Utilisé `player.getFirstname()` et `player.getLastname()` au lieu de `player.getId()`

#### 4.2 Liens de modification

**AVANT :**
```twig
<a href="joueursUpdate.php?id={{ player.getId() }}" class="player-card-link">
```

**APRÈS :**
```twig
<a href="joueursUpdate.php?firstname={{ player.getFirstname() }}&lastname={{ player.getLastname() }}" class="player-card-link">
```

**Ce qui a été fait :**
1. ✅ Remplacé `?id=...` par `?firstname=...&lastname=...`

#### 4.3 Comparaison de relations

**AVANT :**
```twig
{% for item in playerTeam %}
    {% set teamRelation = item.playerTeam %}
    {% if teamRelation.getPlayerId() == player.getId() %}
        {% for team in teams %}
            {% if team.getId() == teamRelation.getTeamId() %}
                {% set player_teams = player_teams|merge([{
                    'team_name': team.getName(),
                    'role': teamRelation.getRole()
                }]) %}
            {% endif %}
        {% endfor %}
    {% endif %}
{% endfor %}
```

**APRÈS :**
```twig
{% for item in playerTeam %}
    {% set teamRelation = item.playerTeam %}
    {% if teamRelation.getPlayer().getFirstname() == player.getFirstname() and
         teamRelation.getPlayer().getLastname() == player.getLastname() %}
        {% set player_teams = player_teams|merge([{
            'team_name': teamRelation.getTeam().getName(),
            'role': teamRelation.getRole()
        }]) %}
    {% endif %}
{% endfor %}
```

**Ce qui a été fait :**
1. ✅ Remplacé `teamRelation.getPlayerId() == player.getId()` par comparaison de firstname + lastname
2. ✅ Simplifié la boucle imbriquée : plus besoin de chercher la team, elle est dans l'objet
3. ✅ Utilisé `teamRelation.getTeam().getName()` directement

#### 4.4 Formulaires de sélection

**AVANT :**
```twig
<select name="team_id" id="team_{{ player.getId() }}" required>
    <option value="">Sélectionner une équipe</option>
    {% for team in teams %}
        {% if not isInTeam %}
            <option value="{{ team.getId() }}">{{ team.getName() }}</option>
        {% endif %}
    {% endfor %}
</select>
```

**APRÈS :**
```twig
<select name="team_name" id="team_{{ player.getFirstname() }}_{{ player.getLastname() }}" required>
    <option value="">Sélectionner une équipe</option>
    {% for team in teams %}
        {% if not isInTeam %}
            <option value="{{ team.getName() }}">{{ team.getName() }}</option>
        {% endif %}
    {% endfor %}
</select>
```

**Ce qui a été fait :**
1. ✅ Changé `name="team_id"` → `name="team_name"`
2. ✅ Changé `value="{{ team.getId() }}"` → `value="{{ team.getName() }}"`
3. ✅ Changé l'ID du select pour être unique avec firstname + lastname

---

## 📊 Exemples concrets avant/après

### Exemple 1 : Créer un match

**AVANT (avec IDs) :**
```php
// Page matchs.php
if (isset($_POST['team_id'], $_POST['opposing_club_id'])) {
    $team_id = (int)$_POST['team_id'];           // ID technique
    $opposing_club_id = (int)$_POST['opposing_club_id'];  // ID technique

    $match = new FootballMatch(
        null,                  // ID auto-généré
        new DateTime($date),
        $city,
        $team_score,
        $opponent_score,
        $team_id,             // Référence par ID
        $opposing_club_id     // Référence par ID
    );

    $matchManager->insert($match);
}

// Template Twig
<select name="team_id">
    <option value="{{ team.getId() }}">{{ team.getName() }}</option>
</select>
```

**APRÈS (OOP pur) :**
```php
// Page matchs.php
if (isset($_POST['team_name'], $_POST['opposing_club_city'])) {
    $team_name = trim($_POST['team_name']);          // Identifiant métier
    $opposing_club_city = trim($_POST['opposing_club_city']);  // Identifiant métier

    // ✅ Charger les objets complets
    $team = !empty($team_name) ? $teamManager->findByName($team_name) : null;
    $opposingClub = $opposingClubManager->findByCity($opposing_club_city);

    if (!$opposingClub) {
        $validator->addError("Club adverse introuvable");
    } else {
        $match = new FootballMatch(
            // Plus d'ID
            new DateTime($date),
            $city,
            $team_score,
            $opponent_score,
            $team,          // ✅ Objet complet
            $opposingClub   // ✅ Objet complet
        );

        $matchManager->insert($match);  // Le Manager résoudra les IDs en interne
    }
}

// Template Twig
<select name="team_name">
    <option value="{{ team.getName() }}">{{ team.getName() }}</option>
</select>
```

### Exemple 2 : Afficher les équipes d'un joueur

**AVANT (avec IDs) :**
```twig
{# Template joueurs.twig #}
{% for item in playerTeam %}
    {% set teamRelation = item.playerTeam %}
    {# Comparaison par ID #}
    {% if teamRelation.getPlayerId() == player.getId() %}
        {# Chercher la team par ID #}
        {% for team in teams %}
            {% if team.getId() == teamRelation.getTeamId() %}
                <div>{{ team.getName() }}</div>
            {% endif %}
        {% endfor %}
    {% endif %}
{% endfor %}
```

**APRÈS (OOP pur) :**
```twig
{# Template joueurs.twig #}
{% for item in playerTeam %}
    {% set teamRelation = item.playerTeam %}
    {# ✅ Comparaison métier #}
    {% if teamRelation.getPlayer().getFirstname() == player.getFirstname() and
         teamRelation.getPlayer().getLastname() == player.getLastname() %}
        {# ✅ Accès direct à l'objet #}
        <div>{{ teamRelation.getTeam().getName() }}</div>
    {% endif %}
{% endfor %}
```

---

## 🚧 Défis rencontrés et solutions

### Défi 1 : Renommer un joueur/équipe

**Problème :**
```php
// ❌ Impossible avec un simple UPDATE
UPDATE player SET firstname = 'NewName' WHERE firstname = 'OldName' AND lastname = 'Doe'
// Si firstname est l'identifiant, on ne peut pas le modifier dans le WHERE !
```

**Solution adoptée :**
```php
// ✅ DELETE + INSERT si les noms changent
if ($old_firstname !== $prenom || $old_lastname !== $nom) {
    $playerManager->delete($oldPlayer);  // Supprime l'ancien
    $playerManager->insert($newPlayer);  // Crée le nouveau
} else {
    $playerManager->update($player);     // Update simple si noms inchangés
}
```

**Conséquence :**
- ⚠️ Les relations PlayerTeam seront perdues si on change le nom (car basées sur player_id qui change)
- **Recommandation** : Ne pas renommer les joueurs/staff après création

### Défi 2 : Résolution des IDs dans les Managers

**Problème :**
```php
// Le code métier manipule des objets
$playerTeam = new PlayerTeam($player, $team, $role);

// Mais la BDD a besoin d'IDs pour les FK
INSERT INTO player_has_team (player_id, team_id, role) VALUES (?, ?, ?)
```

**Solution adoptée :**
```php
// Le Manager fait le pont
public function insert(object $object): bool {
    // Résoudre Player → player_id
    $stmtPlayer = $this->db->prepare("SELECT id FROM player WHERE firstname = ? AND lastname = ?");
    $stmtPlayer->execute([
        $object->getPlayer()->getFirstname(),
        $object->getPlayer()->getLastname()
    ]);
    $playerId = $stmtPlayer->fetchColumn();

    // Résoudre Team → team_id
    $stmtTeam = $this->db->prepare("SELECT id FROM team WHERE name = ?");
    $stmtTeam->execute([$object->getTeam()->getName()]);
    $teamId = $stmtTeam->fetchColumn();

    // Insérer avec IDs
    $stmt = $this->db->prepare("INSERT INTO player_has_team (player_id, team_id, role) VALUES (?, ?, ?)");
    return $stmt->execute([$playerId, $teamId, $object->getRole()->value]);
}
```

**Conséquence :**
- ✅ Le code métier reste pur (manipule des objets)
- ⚠️ Requêtes supplémentaires (impact performance)

### Défi 3 : Hydratation des objets dans findAll()

**Problème :**
```php
// Avant : on retournait juste des IDs
return new PlayerTeam($data['player_id'], $data['team_id'], $role);

// Maintenant : il faut des objets complets
return new PlayerTeam($player, $team, $role);  // $player et $team doivent être créés
```

**Solution adoptée :**
```php
// ✅ JOINs dans la requête pour récupérer toutes les données
$sql = "SELECT p.firstname, p.lastname, p.birthdate, p.picture,
               t.name as team_name, pht.role
        FROM player_has_team pht
        JOIN player p ON pht.player_id = p.id
        JOIN team t ON pht.team_id = t.id";

// ✅ Créer les objets dans la boucle
while ($data = $stmt->fetch()) {
    $player = new Player($data['firstname'], $data['lastname'], ...);
    $team = new Team($data['team_name']);
    $playerTeams[] = new PlayerTeam($player, $team, $role);
}
```

**Conséquence :**
- ✅ Un seul SELECT avec JOINs (performant)
- ✅ Objets complets retournés

### Défi 4 : Signatures incompatibles dans ManagerInterface

**Problème :**
```php
interface ManagerInterface {
    public function update(object $object): bool;
}

// Mais TeamManager a besoin de l'ancien nom
class TeamManager implements ManagerInterface {
    public function update(object $object, string $oldName): bool  // ❌ Incompatible
}
```

**Solution adoptée :**
```php
// ✅ Retirer update() de l'interface
interface ManagerInterface {
    public function findAll(): array;
    public function insert(object $object): bool;
    public function delete(object $object): bool;
    // Plus de update() : chaque Manager a sa propre signature
}
```

**Conséquence :**
- ✅ Chaque Manager peut avoir sa propre signature pour update()
- ⚠️ Moins de polymorphisme (mais acceptable ici)

---

## ⚠️ Contraintes et limitations

### 1. Unicité obligatoire des identifiants métier

**Contrainte stricte :**
```sql
-- Ces combinaisons DOIVENT être uniques :
- Player/StaffMember : (firstname, lastname)
- Team : (name)
- OpposingClub : (city, name)
- Match : (date, city)
```

**Si doublons :**
```php
// ❌ Si 2 joueurs s'appellent "Jean Dupont"
$player1 = $playerManager->findByName("Jean", "Dupont");  // Lequel ?
```

**Vérification recommandée :**
```sql
-- Avant migration, vérifier l'unicité
SELECT firstname, lastname, COUNT(*)
FROM player
GROUP BY firstname, lastname
HAVING COUNT(*) > 1;
```

### 2. Impossibilité de renommer facilement

**Problème :**
```php
// ❌ Renommer un joueur = perdre ses relations
$player = $playerManager->findByName("Jean", "Dupont");
// Si on le renomme en "John", toutes les PlayerTeam pointant vers "Jean Dupont" seront orphelines
```

**Workaround actuel :**
```php
// DELETE + INSERT
// ⚠️ Les PlayerTeam sont perdues car elles pointent vers l'ancien player_id
```

**Recommandation :**
- Éviter de renommer après création
- Ou accepter de recréer les relations manuellement

### 3. Performance : requêtes supplémentaires

**Impact :**
```php
// Avant : 1 requête
INSERT INTO player_has_team VALUES (1, 2, 'Attaquant');

// Après : 3 requêtes
SELECT id FROM player WHERE firstname = ? AND lastname = ?;  // +1 requête
SELECT id FROM team WHERE name = ?;                          // +1 requête
INSERT INTO player_has_team VALUES (1, 2, 'Attaquant');
```

**Mitigation possible :**
- Utiliser un cache pour les résolutions Player/Team → ID
- Accepter le surcoût (acceptable pour une petite application)

### 4. BDD inchangée mais logique plus complexe

**Situation :**
- La BDD a toujours des colonnes `id` et des clés étrangères
- Les Managers font le mapping entre monde métier (objets) et persistance (IDs)
- Plus de code dans les Managers

**Conséquence :**
- ✅ Aucune migration BDD nécessaire
- ⚠️ Managers plus complexes à maintenir

---

## 📖 Guide d'utilisation

### Créer une entité

```php
// ✅ Player
$player = new Player("Jean", "Dupont", new DateTime("1995-01-01"), "photo.jpg");
$playerManager->insert($player);

// ✅ Team
$team = new Team("Équipe A");
$teamManager->insert($team);

// ✅ StaffMember
$staff = new StaffMember("Marie", "Martin", StaffRole::Entraineur, "photo.jpg");
$staffMemberManager->insert($staff);
```

### Récupérer une entité

```php
// ✅ Par identifiants métier
$player = $playerManager->findByName("Jean", "Dupont");
$team = $teamManager->findByName("Équipe A");
$match = $matchManager->findByDateAndCity("2025-01-15 20:00:00", "Paris");
```

### Modifier une entité

```php
// ✅ Si on NE change PAS les identifiants
$player = $playerManager->findByName("Jean", "Dupont");
$player->setBirthdate("1996-01-01");
$playerManager->update($player);

// ⚠️ Si on change le nom (éviter si possible)
// Utiliser le formulaire joueursUpdate.php qui gère le DELETE + INSERT
```

### Créer une relation

```php
// ✅ Charger les objets complets
$player = $playerManager->findByName("Jean", "Dupont");
$team = $teamManager->findByName("Équipe A");

// ✅ Créer la relation
$playerTeam = new PlayerTeam($player, $team, PlayerRole::Attaquant);
$playerTeamManager->insert($playerTeam);
// Le Manager résoudra automatiquement les IDs en interne
```

### Supprimer une entité

```php
// ✅ Charger d'abord
$player = $playerManager->findByName("Jean", "Dupont");

// ✅ Supprimer
$playerManager->delete($player);
// Le Manager utilise firstname + lastname dans le WHERE
```

---

## 🎓 Conclusion

Cette migration vers une architecture OOP pure a consisté à :

1. **Retirer tous les IDs des objets métier** pour qu'ils n'exposent que des concepts métier
2. **Utiliser des identifiants naturels** (firstname+lastname, name, date+city) au lieu d'IDs techniques
3. **Faire des Managers un pont** entre le monde métier (objets) et la persistance (BDD avec IDs)
4. **Adapter toute l'application** (classes, managers, pages PHP, templates Twig) pour manipuler des objets au lieu d'IDs
5. **Gérer les contraintes** (unicité, renommage, performance) avec des solutions pragmatiques

**Résultat :**
- ✅ Code métier découplé de la persistance
- ✅ Relations exprimées naturellement avec des objets
- ✅ Architecture conforme aux principes OOP et DDD
- ⚠️ Contraintes d'unicité strictes
- ⚠️ Renommage difficile
- ⚠️ Légère baisse de performance (acceptable)

**Cette approche convient pour :**
- Projets éducatifs pour comprendre l'OOP pure
- Applications où les identifiants naturels sont stables
- Contextes où la séparation domaine/infrastructure est prioritaire

**Pour aller plus loin :**
- Utiliser un ORM (Doctrine, Eloquent) qui gère tout cela automatiquement
- Implémenter des Value Objects pour les identifiants
- Adopter une architecture hexagonale complète
