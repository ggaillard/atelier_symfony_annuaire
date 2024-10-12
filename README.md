# Atelier : Création d'une application Symfony pour un annuaire
## Objectif

L'objectif de cet atelier est de réaliser une application Symfony simple permettant d'afficher une liste de personnes avec leur prénom et nom, à partir d'une base de données. L'atelier commence par la création d'un diagramme de cas d'utilisation pour modéliser les interactions de l'utilisateur avec l'application.

## Étape 1 : Création d'un diagramme de cas d'utilisation avec PlantUML

Avant de commencer à coder, vous allez créer un diagramme de cas d'utilisation pour modéliser l'interaction de l'utilisateur avec l'application d'annuaire.

> "En tant qu'utilisateur, je souhaite consulter la liste des personnes dans l'annuaire."

### Instructions

1. **Créer un répertoire pour les diagrammes**  
    Dans le dossier de votre projet, créez un répertoire `docs/diagrams` pour stocker les diagrammes UML que vous allez générer.
    ```bash
    mkdir -p docs/diagrams
    ```

2. **Créer le fichier PlantUML**  
    Créez un fichier `use_case.puml` dans le répertoire `docs/diagrams` :
    ```bash
    touch docs/diagrams/use_case.puml
    ```

3. **Ajouter le diagramme de cas d'utilisation**  
    Éditez le fichier `use_case.puml` et ajoutez le code suivant pour modéliser un simple cas d'utilisation :
    ```plantuml
    @startuml
    actor Utilisateur
    rectangle Annuaire {
         Utilisateur --> (Consulter la liste des personnes)
    }
    @enduml
    ```

4. **Installation de Java**  
    Avant de générer des diagrammes avec PlantUML, assurez-vous que Java est installé sur votre système.

    #### Vérification de l'installation de Java
    Exécutez la commande suivante pour vérifier si Java est déjà installé :
    ```bash
    java -version
    ```

5. **Générer le diagramme**  
    Utilisez PlantUML pour générer un fichier PNG à partir du fichier `.puml`. Exécutez la commande suivante pour créer le fichier PNG du diagramme :
    ```bash
    plantuml docs/diagrams/use_case.puml
    ```

    Le fichier `use_case.png` sera généré dans le répertoire `docs/diagrams`.

6. **Vérification**  
    Ouvrez le fichier `docs/diagrams/use_case.png` pour vérifier que le diagramme de cas d'utilisation a été correctement généré.

---

## Étape 2 : Création de l'application Symfony

Maintenant que vous avez modélisé le cas d'utilisation de votre application, vous allez créer l'application Symfony.

### 1. Création du projet Symfony

Créez un nouveau projet Symfony avec la commande suivante :
```bash
symfony new annuaire --full
cd annuaire
```

#### Vérification
Lancez le serveur Symfony pour vérifier que le projet a été correctement créé :
```bash
symfony server:start  // affiche le log dans le terminal
```
ou
```bash
symfony serve -d   // Serveur en arrière plan
```
Accédez à `http://localhost:8000` dans votre navigateur et vérifiez que la page d'accueil Symfony s'affiche.

---

### 2. Configuration de la base de données

#### Utilisation de MySQL

Si vous préférez utiliser MySQL, modifiez le fichier `.env.local` pour définir les informations de connexion à votre base de données MySQL :
```env
DATABASE_URL="mysql://db_user:db_password@127.0.0.1:3306/annuaire"
```

Vérifiez si MySQL est installé sur votre machine Windows :
```bash
mysql --version
```

Après la configuration, créez la base de données en utilisant la commande suivante :
```bash
symfony console doctrine:database:create
```

Vérifiez que la base de données est bien créée sans erreurs.

### 3. Création de l'entité `Personne`

Créez une entité `Personne` avec les champs `nom` et `prenom` :
```bash
symfony console make:entity Personne
```
Dans le fichier généré, ajoutez les champs pour le nom et le prénom.

#### Vérification
Générez une migration pour créer la table `personne` dans la base de données :
```bash
symfony console make:migration
symfony console doctrine:migrations:migrate
```
Validez le schéma de la base de données avec la commande suivante :
```bash
symfony console doctrine:schema:validate
```

---

### 4. Création du contrôleur `AnnuaireController`

Créez un contrôleur pour gérer l'affichage de la liste des personnes :
```bash
symfony console make:controller AnnuaireController
```
Dans le fichier généré, modifiez la méthode pour récupérer la liste des personnes depuis la base de données et les afficher dans une vue.

```php
<?php
namespace App\Controller;

use App\Entity\Personne;
use App\Repository\PersonneRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AnnuaireController extends AbstractController
{
    
    #[Route('/', name: 'default_route')]
    public function defaultRoute(): Response
    {
        // Rediriger vers la vue base.html.twig
        return $this->render('base.html.twig');
    }

    #[Route('/annuaire', name: 'liste_personnes')]
    public function listePersonnes(PersonneRepository $personnesRepository, EntityManagerInterface $entityManager): Response
    {
        // Récupérer toutes les personnes depuis la base de données
        $personnes = $personnesRepository->findAll();

        // Passer les personnes récupérées à la vue pour l'affichage
        return $this->render('annuaire/liste_personnes.html.twig', [
            'personnes' => $personnes,
        ]);
    }
}
```

#### Vérification
Lancez le serveur Symfony et accédez à l'URL `/annuaire_liste_personnes` dans votre navigateur pour vérifier que la page s'affiche correctement.

---

### 5. Création de la vue

Ajouter cette ligne dans la vue `base.html.twig` :
```twig
<a href="{{ path('liste_personnes') }}">Liste des personnes</a>
```


Créez une vue `liste_personnes.html.twig` dans le dossier `templates/annuaire` pour afficher la liste des personnes :
```twig
<h1>Annuaire des personnes</h1>

<ul>
     {% for personne in personnes %}
          <li>{{ personne.prenom }} {{ personne.nom }}</li>
     {% endfor %}
</ul>
```

#### Vérification
Ajoutez des personnes dans la base de données via une commande SQL :
```bash
symfony console doctrine:query:sql "INSERT INTO personne (nom, prenom) VALUES ('Platini', 'Michel');"
symfony console doctrine:query:sql "INSERT INTO personne (nom, prenom) VALUES ('Rocheteau', 'Dominique');"
symfony console doctrine:query:sql "INSERT INTO personne (nom, prenom) VALUES ('Revelli', 'Hervé');"
symfony console doctrine:query:sql "INSERT INTO personne (nom, prenom) VALUES ('Janvion', 'Gérard');"
symfony console doctrine:query:sql "INSERT INTO personne (nom, prenom) VALUES ('Santini', 'Jean-Michel');"
```

Rafraîchissez la page `/annuaire` pour vérifier que les noms et prénoms s'affichent correctement.

---

## Étape 6 : Remplir la base de données avec des DataFixtures

Nous allons maintenant utiliser des **DataFixtures** pour remplir la base de données avec des données fictives.

### 1. Installation de DoctrineFixturesBundle

Commencez par installer le bundle `DoctrineFixturesBundle` qui nous permettra de charger des données de test :
```bash
composer require --dev orm-fixtures
```

### 2. Installer Faker

Assurez-vous que la bibliothèque Faker est installée :
```bash
composer require fakerphp/faker --dev
```

### 3. Créer une fixture pour l'entité `Personne`

Créez une classe de fixture pour générer des données de test dans la base de données :
```bash
symfony console make:fixture PersonneFixtures
```

Dans la classe générée, modifiez le fichier pour ajouter plusieurs personnes dans la base de données :
```php
<?php

namespace App\DataFixtures;

use App\Entity\Personne;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class PersonneFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR'); // Crée une instance de Faker pour générer des données fictives en français

        for ($i = 1; $i <= 10; $i++) {
            $personne = new PersonneEntity();
            $personne->setPrenom($faker->firstName);
            $personne->setNom($faker->lastName);
            $manager->persist($personne);
        }

        $manager->flush(); // Enregistre les données dans la base de données
    }
}
```

### 4. Charger les fixtures dans la base de données

Exécutez la commande suivante pour charger les fixtures dans la base de données :
```bash
symfony console doctrine:fixtures:load
```

#### Vérification

Une fois les fixtures chargées, accédez de nouveau à la page `/annuaire` pour vérifier que la liste des personnes est correctement affichée.
```
Dans la classe générée, modifiez le fichier pour ajouter plusieurs personnes dans la base de données :
```php
<?php

namespace App\DataFixtures;

use App\Entity\Personne;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class PersonneFixtures extends Fixture
{
     public function load(ObjectManager $manager): void
     {
          $faker = Factory::create();

          for ($i = 1; $i <= 10; $i++) {
                $personne = new Personne();
                $personne->setPrenom($faker->firstName);
                $personne->setNom($faker->lastName);
                $manager->persist($personne);
          }

          $manager->flush();
     }
}
```

### 3. Charger les fixtures dans la base de données

Exécutez la commande suivante pour charger les fixtures dans la base de données :
```bash
symfony console doctrine:fixtures:load
```

#### Vérification
Une fois les fixtures chargées, accédez de nouveau à la page `/annuaire` pour vérifier que la liste des personnes est correctement affichée.

---

## Étape 7 : Partage du résultat

Lorsque vous avez terminé toutes les étapes, partagez le lien de votre dépôt GitHub avec le professeur. Assurez-vous d'y inclure :
- Le code source de l'application Symfony.
- Le fichier PNG du diagramme de cas d'utilisation généré par PlantUML (`docs/diagrams/use_case.png`).
- Les fixtures utilisées pour remplir la base de données (`src/DataFixtures/PersonneFixtures.php`).

---

### Suite.md : Extension du cas d'utilisation

Pour aller plus loin, vous pouvez ajouter des cas d'utilisation supplémentaires, tels que :
- "En tant qu'utilisateur, je souhaite ajouter une adresse e-mail."
- "En tant qu'utilisateur, je souhaite ajouter une personne à l'annuaire."
- "En tant qu'utilisateur, je souhaite rechercher une personne par son nom."

---

### License

Creative Commons pour le texte et MIT pour le code.
