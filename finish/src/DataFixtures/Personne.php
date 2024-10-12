<?php

/**
 * Classe Personne
 * 
 * Cette classe est une fixture pour l'entité Personne. Elle utilise la librairie Faker pour générer des données fictives.
 * 
 * @package App\DataFixtures
 */

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use App\Entity\Personne as PersonneEntity;

class Personne extends Fixture
{
    /**
     * Charge les données de test pour l'entité Personne.
     * 
     * Cette méthode crée 10 instances de l'entité Personne avec des prénoms et noms générés aléatoirement,
     * puis les persiste dans la base de données.
     * 
     * @param ObjectManager $manager Le gestionnaire d'entités de Doctrine.
     */
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
