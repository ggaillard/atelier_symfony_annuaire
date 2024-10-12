
# Extension du cas d'utilisation : 

## Objectif
L'objectif de cette extension est d'ajouter des fonctionnalités supplémentaires à l'application d'annuaire.

### Cas d'utilisation supplémentaires

1. **En tant qu'utilisateur, je souhaite ajouter l'adresse email d'une personne.**
2. **En tant qu'utilisateur, je souhaite ajouter une personne dans l'annuaire.**
3. **En tant qu'utilisateur, je souhaite rechercher une personne par son nom.**

## 1. Ajouter l'adresse email d'une personne

### Étape 1 : Ajout de l'attribut `email` à l'entité `Personne`
Modifiez l'entité `Personne` pour ajouter un champ `email` :
```bash
symfony console make:entity Personne
```
Lorsque vous y êtes invité, répondez aux questions suivantes :

- **Nom du champ** : `email`
- **Type de champ** : `string`
- **Longueur du champ** : `255` (ou appuyez sur Entrée pour utiliser la valeur par défaut)
- **Autoriser null** : `yes` (pour rendre l'email optionnel)

#### Vérification :
Mettez à jour la base de données avec la commande suivante :
```bash
symfony console make:migration
symfony console doctrine:migrations:migrate
```

## 2. Ajouter une personne dans l'annuaire

### Fonctionnalité : Ajouter une personne dans l'annuaire

#### Objectif :
Permettre à un utilisateur d'ajouter une personne (avec un prénom, un nom et un email) dans l'annuaire via un formulaire.

### Étapes pour implémenter cette fonctionnalité

#### Étape 1 : Créer un formulaire d'ajout de personne

1. Utiliser `make:form` pour créer le formulaire :
    ```bash
    symfony console make:form PersonneType
    ```
2. Modifiez le fichier généré dans `src/Form/PersonneType.php` pour inclure les champs `prenom`, `nom`, et `email` :
     ```php
     <?php

     namespace App\Form;

     use App\Entity\Personne;
     use Symfony\Component\Form\AbstractType;
     use Symfony\Component\Form\Extension\Core\Type\EmailType;
     use Symfony\Component\Form\Extension\Core\Type\TextType;
     use Symfony\Component\Form\FormBuilderInterface;
     use Symfony\Component\OptionsResolver\OptionsResolver;

     class PersonneType extends AbstractType
     {
          public function buildForm(FormBuilderInterface $builder, array $options): void
          {
                $builder
                     ->add('prenom', TextType::class, [
                          'label' => 'Prénom',
                     ])
                     ->add('nom', TextType::class, [
                          'label' => 'Nom',
                     ])
                     ->add('email', EmailType::class, [
                          'label' => 'Email',
                          'required' => false, // Pour rendre l'email optionnel
                     ]);
          }

          public function configureOptions(OptionsResolver $resolver): void
          {
                $resolver->setDefaults([
                     'data_class' => Personne::class,
                ]);
          }
     }
     ```

#### Étape 2 : Ajouter la logique dans le contrôleur

1. Créer la méthode `ajouterPersonne` dans le contrôleur :
     ```php
     <?php

     namespace App\Controller;

     use App\Entity\Personne;
     use App\Form\PersonneType;
     use Doctrine\ORM\EntityManagerInterface;
     use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
     use Symfony\Component\HttpFoundation\Request;
     use Symfony\Component\HttpFoundation\Response;
     use Symfony\Component\Routing\Annotation\Route;

     class AnnuaireController extends AbstractController
     {
          #[Route('/annuaire/ajouter', name: 'annuaire_ajouter_personne')]
          public function ajouterPersonne(Request $request, EntityManagerInterface $entityManager): Response
          {
                $personne = new Personne();

                // Création du formulaire
                $form = $this->createForm(PersonneType::class, $personne);
                $form->handleRequest($request);

                // Traitement du formulaire lors de sa soumission
                if ($form->isSubmitted() && $form->isValid()) {
                     $entityManager->persist($personne);
                     $entityManager->flush();

                     // Redirection après l'ajout de la personne
                     return $this->redirectToRoute('annuaire_liste_personnes');
                }

                // Affichage du formulaire
                return $this->render('annuaire/ajouter.html.twig', [
                     'form' => $form->createView(),
                ]);
          }
     }
     ```

#### Étape 3 : Créer la vue pour le formulaire d'ajout

1. Créer le fichier `ajouter.html.twig` dans le dossier `templates/annuaire` :
     ```twig
     {# templates/annuaire/ajouter.html.twig #}

     <h1>Ajouter une nouvelle personne dans l'annuaire</h1>

     {{ form_start(form) }}
          {{ form_row(form.prenom) }}
          {{ form_row(form.nom) }}
          {{ form_row(form.email) }}
          <button type="submit">Ajouter</button>
     {{ form_end(form) }}
     ```

#### Vérification :
Accédez à l'URL `/annuaire/ajouter` et ajoutez une nouvelle personne.

## 3. Rechercher une personne par son nom

### Étape 1 : Créer un formulaire de recherche

1. Utiliser la commande `make:form` pour créer un formulaire dédié à la recherche :
     ```bash
     symfony console make:form RecherchePersonneType
     ```
2. Lorsque vous y êtes invité, choisissez `App\Entity\Personne` comme entité de référence.
3. Dans le fichier généré (`src/Form/RecherchePersonneType.php`), modifiez le champ à inclure uniquement la recherche par nom :
     ```php
     <?php

     namespace App\Form;

     use Symfony\Component\Form\AbstractType;
     use Symfony\Component\Form\Extension\Core\Type\TextType;
     use Symfony\Component\Form\FormBuilderInterface;
     use Symfony\Component\OptionsResolver\OptionsResolver;

     class RecherchePersonneType extends AbstractType
     {
          public function buildForm(FormBuilderInterface $builder, array $options): void
          {
                $builder
                     ->add('nom', TextType::class, [
                          'label' => 'Rechercher par nom',
                          'required' => false,
                     ]);
          }

          public function configureOptions(OptionsResolver $resolver): void
          {
                $resolver->setDefaults([]);
          }
     }
     ```

### Étape 2 : Ajouter la méthode de recherche dans le contrôleur

1. Utiliser la commande `make:controller` pour créer une méthode de recherche :
     ```bash
     symfony console make:controller AnnuaireController
     ```
2. Ouvrez le fichier généré dans `src/Controller/AnnuaireController.php` et ajoutez une nouvelle route pour gérer la recherche de personnes :
     ```php
     <?php

     namespace App\Controller;

     use App\Form\RecherchePersonneType;
     use App\Repository\PersonneRepository;
     use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
     use Symfony\Component\HttpFoundation\Request;
     use Symfony\Component\HttpFoundation\Response;
     use Symfony\Component\Routing\Annotation\Route;

     class AnnuaireController extends AbstractController
     {
          private $personneRepository;

          public function __construct(PersonneRepository $personneRepository)
          {
                $this->personneRepository = $personneRepository;
          }

          #[Route('/annuaire/recherche', name: 'annuaire_recherche')]
          public function rechercherPersonne(Request $request): Response
          {
                $form = $this->createForm(RecherchePersonneType::class);
                $form->handleRequest($request);

                $personnes = [];

                if ($form->isSubmitted() && $form->isValid()) {
                     $nom = $form->get('nom')->getData();
                     $personnes = $this->personneRepository->findBy(['nom' => $nom]);
                }

                return $this->render('annuaire/recherche.html.twig', [
                     'form' => $form->createView(),
                     'personnes' => $personnes,
                ]);
          }
     }
     ```

### Étape 3 : Créer la vue de recherche

1. Créez un fichier `recherche.html.twig` dans le dossier `templates/annuaire` :
     ```twig
     {# templates/annuaire/recherche.html.twig #}

     <h1>Recherche dans l'annuaire</h1>

     {{ form_start(form) }}
          {{ form_row(form.nom) }}
          <button type="submit">Rechercher</button>
     {{ form_end(form) }}

     <h2>Résultats</h2>
     <ul>
          {% for personne in personnes %}
                <li>{{ personne.prenom }} {{ personne.nom }}</li>
          {% else %}
                <li>Aucun résultat trouvé</li>
          {% endfor %}
     </ul>
     ```

#### Vérification :
Accédez à l'URL `/annuaire/recherche?nom=NomCherche` pour vérifier que la recherche fonctionne.


