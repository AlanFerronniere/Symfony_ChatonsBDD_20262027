<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Form\CategorieSupprimerType;
use App\Form\CategorieType;
use App\Repository\CategorieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CategorieController extends AbstractController
{
    #[Route('/', name: 'app_categorie')]
    public function index(CategorieRepository $categorieRepository): Response
    {
        //je vais chercher les catégories (select * )
        $categories = $categorieRepository->findAll();

        return $this->render('categorie/index.html.twig', [
            "categories" => $categories,
        ]);
    }

    #[Route('/ajouter-categorie', name: 'app_ajouter_categorie')]
    public function ajouter(Request $request
        , EntityManagerInterface $em): Response
    {
        //Je crée un formulaire pour ajouter une catégorie
        //1 je crée une catégorie vide
        $categorie = new Categorie();
        //crée un formulaire
        $form = $this->createForm(CategorieType::class, $categorie);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($categorie); //je veux faire un insert
            $em->flush(); //je déclenche l'insert

            //retour à l'accueil
            return $this->redirectToRoute('app_categorie');
        }

        return $this->render('categorie/ajouter.html.twig', [
            "formulaire" => $form->createView(),
        ]);
    }

    #[Route('/modifier-categorie/{id}', name: 'app_modifier_categorie')]
    public function modifier(Request $request
        , CategorieRepository $categorieRepository
        , EntityManagerInterface $em, $id): Response
    {
        //Je crée un formulaire pour modifier une catégorie
        //1 je vais chercher la catégorie
        $categorie = $categorieRepository->find($id);
        //crée un formulaire
        $form = $this->createForm(CategorieType::class, $categorie);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($categorie); //je veux faire un update
            $em->flush(); //je déclenche l'update

            //retour à l'accueil
            return $this->redirectToRoute('app_categorie');
        }

        return $this->render('categorie/modifier.html.twig', [
            "formulaire" => $form->createView(),
        ]);
    }

    #[Route('/supprimer-categorie/{id}', name: 'app_supprimer_categorie')]
    public function supprimer(Request $request
        , CategorieRepository $categorieRepository
        , EntityManagerInterface $em, $id): Response
    {
        ///Je crée un formulaire pour modifier une catégorie
        //1 je vais chercher la catégorie
        $categorie = $categorieRepository->find($id);
        //crée un formulaire
        $form = $this->createForm(CategorieSupprimerType::class, $categorie);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            //on supprime les chatons de la catégorie avant de supprimer la catégorie
            $chatons = $categorie->getChatons();
            foreach ($chatons as $chaton) {
                //on supprime la photo du chaton si elle existe
                $photo = $chaton->getPhoto();
                if ($photo) {
                    $fs = new Filesystem();
                    if ($fs->exists($photo)) {
                        $fs->remove($photo);
                    }
                }
                $em->remove($chaton);
            }

            $em->remove($categorie); //je veux faire un delete
            $em->flush(); //je déclenche le delete

            //retour à l'accueil
            return $this->redirectToRoute('app_categorie');
        }

        return $this->render('categorie/supprimer.html.twig', [
            "categorie" => $categorie,
            "formulaire" => $form->createView(),
        ]);
    }
}
