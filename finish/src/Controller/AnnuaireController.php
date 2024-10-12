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
