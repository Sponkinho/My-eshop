<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\Produit;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DefaultController extends AbstractController
{
    #[Route("/", name: 'default_home', methods: ['GET'])]
    public function home(EntityManagerInterface $entityManager): Response
    {
        $produits = $entityManager->getRepository(Produit::class)->findBy(['deletedAt' => null]);
        
        return $this->render('default/home.html.twig', [
            'produits' => $produits
        ]);
    }

    #[Route("/voi-mes-infos", name: 'show_profile', methods: ['GET'])]
    public function showProfile(EntityManagerInterface $entityManager): Response
    {
        $commands = $entityManager->getRepository(Commande::class)->findBy(['deletedAt' => null]);
        
        return $this->render('default/show_profile.html.twig', [
            'commands' => $commands
        ]);
    }
}
