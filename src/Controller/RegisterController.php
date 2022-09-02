<?php

namespace App\Controller;

use DateTime;
use App\Entity\User;
use App\Form\RegisterFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;



class RegisterController extends AbstractController
{
    #[Route("/inscription", name: 'user_register', methods: ['GET', 'POST'])]
    public function register(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {

        # Cette condition permet de detecter si un utilisateur est connecté, si oui alors l'utilisateur est redirigé. Cela interdit l'inscription à un user connecté.
        if ($this->getUser()) {
            return $this->redirectToRoute('default_home');
        }

        # 1 - Instanciation
        $user = new User();

        # 2 - Création du formulaire + mécanisme d'auto-hydratation
        $form = $this->createForm(RegisterFormType::class, $user)
        ->handleRequest($request);

        # 4 - Au clic du bouton 'Valider'
        if($form->isSubmitted() && $form->isValid()){
            #Set des propriétés qui ne sont pas dans le formulaire
            $user->setCreatedAt(new DateTime());
            $user->setUpdatedAt(new DateTime());
            # La propriété "roles" est un array
            $user->setRoles(['ROLE_USER']);

            $user->setPassword(
                $passwordHasher->hashPassword($user, $form->get('password')->getData())
            );

            $entityManager->persist($user);
            $entityManager->flush();

            # La méthode addFlash() nous permet d'afficher des messages déstinés ) l'utilisateur, on pourra les afficher en front avec Twig
            $this->addFlash('success', 'Votre inscription a été effectuée avec succès !');
            return $this->redirectToRoute('default_home');
        }

        # 3 - Rendu de la vue Twig, avec le formulaire
        return $this->render("register/form.html.twig", [
            'form' => $form->createView() # createView() permet de générer le HTML pour l'affichage

        ]);
    }
}
