<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Form\ProduitFormType;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Config\Framework\HttpClient\DefaultOptions\RetryFailedConfig;

#[Route('/admin')]
class ProduitController extends AbstractController
{
    #[Route('/voir-les-produits', name: 'show_produits', methods: ['GET'])]
    public function showProduits(EntityManagerInterface $entityManager): Response
    {
        # Récupération en BDD de toutes les entités Produit, grâce au Repository
        $produits = $entityManager->getRepository(Produit::class)->findBy(["deletedAt" => null]);

        return $this->render('admin/produit/show_produits.html.twig', [
            'produits' => $produits
        ]);
    }

    #[Route('/ajouter-un-produit', name: 'add_produits', methods: ['GET', 'POST'])]
    public function addProduits(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $produit = new Produit();

        $form = $this->createForm(ProduitFormType::class, $produit);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $produit->setCreatedAt(new DateTime());
            $produit->setUpdatedAt(new DateTime());

            $photo = $form->get('photo')->getData();

            if ($photo) {
                $this->handleFile($produit, $photo, $slugger);
            }

            $entityManager->persist($produit);
            $entityManager->flush();

            $this->addFlash('success', 'Le produit s\'est importer avec succès !');

            return $this->redirectToRoute('show_produits');
        }

        return $this->render("admin/produit/form/add_produits.html.twig", [
            'form_produit' => $form->createView()
        ]);
    }

    #[Route('/modifier-un-produit/{id}', name: 'update_produit', methods: ['GET', 'POST'])]
    public function updateProduit(Produit $produit, Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        # Récupération de la photo actuelle
        $originalPhoto = $produit->getPhoto();

        $form = $this->createForm(ProduitFormType::class, $produit, [
            'photo' => $originalPhoto
        ])->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $produit->setUpdatedAt(new DateTime());
            $photo = $form->get('photo')->getData();

            if ($photo) {
                $this->handleFile($produit, $photo, $slugger);
            } else {
                $produit->setPhoto($originalPhoto);
            }
        
            $entityManager->persist($produit);
            $entityManager->flush();

            $this->addFlash('success', 'Le produit s\'est modifier avec succès !');

            return $this->redirectToRoute('show_produits');
        }

        return $this->render('admin/produit/form/add_produits.html.twig', [
            'form_produit' => $form->createView(),
            'produit' => $produit
        ]);
    }

    #[Route('/archiver-un-produit/{id}', name: 'soft_delete_produit', methods: ['GET'])]
    public function softDeleteProduit(Produit $produit, EntityManagerInterface $entityManager): RedirectResponse
    {
        $produit->setDeletedAt(new DateTime());

        $entityManager->persist($produit);
        $entityManager->flush();

        $this->addFlash('success', 'Le produit a bien été archivé !');

        return $this->redirectToRoute('show_produits');
    }

    #[Route('/voir-les-archives', name: 'show_trash', methods: ['GET'])]
    public function showTrash(EntityManagerInterface $entityManager): Response
    {
        return $this->render('admin/produit/show_trash.html.twig');
    }
    

    ///////////////////////////////////////////////////////////////// PRIVATE FUNCTION /////////////////////////////////////////////////////////////

    private function handleFile(Produit $produit, UploadedFile $photo, SluggerInterface $slugger): void
    {
        # 1 - Déconstruire le nom du fichier
        $extension = '.' . $photo->guessExtension();

        # 2 - Sécuriser le nom et reconstruire le nouveau nom du fichier
        // $safeFilename = $slugger->slug($photo->getClientOriginalName());
        $safeFilename = $slugger->slug($produit->getTitle());

        $newFilename = $safeFilename . '_' . uniqid() . $extension;

        # 3 - Déplacer le fichier dans le bon dossier
        try {
            // On a défini un paramètre dans config/service.yaml
            $photo->move($this->getParameter('uploads_dir'), $newFilename);
            $produit->setPhoto($newFilename);
        } catch (FileException $exception) {
            // $exception->getMessage();
            $this->addFlash('warning', 'La photo du produit ne s\'est pas importée avec succès. Veuillez réessayer en modifiant le produit.');
            // return $this->redirectToRoute('add_produit');
        }
    }
}
