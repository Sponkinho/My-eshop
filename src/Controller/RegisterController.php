<?php

namespace App\Controller;

use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegisterController extends AbstractController
{
    #[Route("/inscription", name: 'user_register', methods: ['GET', 'POST'])]
    public function register(Request $request, EntityManager $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {





    }
}