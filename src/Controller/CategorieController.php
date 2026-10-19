<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Form\CategorieType;
use App\Repository\CategorieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CategorieController extends AbstractController
{
    #[Route('/', name: 'app_categorie')]
    public function index(Request $request
        , CategorieRepository $categorieRepository
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
        }

        //je vais chercher les catégories (select * )
        $categories = $categorieRepository->findAll();

        return $this->render('categorie/index.html.twig', [
            "categories" => $categories,
            "formulaire" => $form->createView(),
        ]);
    }
}
