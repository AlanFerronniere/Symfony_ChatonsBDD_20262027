<?php

namespace App\Controller;

use App\Entity\Chaton;
use App\Form\ChatonSupprimerType;
use App\Form\ChatonType;
use App\Repository\CategorieRepository;
use App\Repository\ChatonRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class ChatonController extends AbstractController
{
    #[Route('/chatons/{id_categorie}', name: 'app_chatons')]
    public function index($id_categorie, CategorieRepository $repository): Response
    {
        $categorie = $repository->find($id_categorie);

        $chatons = $categorie->getChatons();

        return $this->render('chaton/index.html.twig', [
            "categorie" => $categorie,
            "chatons" => $chatons,
        ]);
    }

    #[Route('/ajouter_chaton', name: 'app_ajouter_chaton')]
    public function ajouterChaton(Request $request,
        EntityManagerInterface $em): Response
    {
        //je crée un chaton vide
        $chaton = new Chaton();
        //un formulaire
        $form = $this->createForm(ChatonType::class, $chaton);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            //il faut gérer la photo
            //Comme on est en prog objet, on va faire une méthode
            $this->gererLaPhoto($form, $chaton);
            $em->persist($chaton);
            $em->flush();

            return $this->redirectToRoute('app_chatons', ['id_categorie' => $chaton->getCategorie()->getId()]);
        }

        return $this->render('chaton/ajouter.html.twig', [
            'formulaire' => $form->createView(),
        ]);
    }

    #[Route('/modifier_chaton/{id}', name: 'app_modifier_chaton')]
    public function modifierChaton($id, Request $request, ChatonRepository $chatonRepository,
                                  EntityManagerInterface $em): Response
    {
        //je récupère le chaton à modifier
        $chaton = $chatonRepository->find($id);
        //un formulaire
        $form = $this->createForm(ChatonType::class, $chaton);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            //il faut gérer la photo
            //Comme on est en prog objet, on va faire une méthode
            $this->gererLaPhoto($form, $chaton);
            $em->persist($chaton);
            $em->flush();

            return $this->redirectToRoute('app_chatons', ['id_categorie' => $chaton->getCategorie()->getId()]);
        }

        return $this->render('chaton/modifier.html.twig', [
            'formulaire' => $form->createView(),
        ]);
    }

    #[Route('/supprimer_chaton/{id}', name: 'app_supprimer_chaton')]
    public function supprimerChaton($id, Request $request, ChatonRepository $chatonRepository,
                                   EntityManagerInterface $em): Response
    {
        //je récupère le chaton à modifier
        $chaton = $chatonRepository->find($id);
        //un formulaire
        $form = $this->createForm(ChatonSupprimerType::class, $chaton);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            //il faut gérer la photo
            //Comme on est en prog objet, on va faire une méthode
            $this->supprimerLaPhoto($chaton);
            $em->remove($chaton);
            $em->flush();

            return $this->redirectToRoute('app_chatons', ['id_categorie' => $chaton->getCategorie()->getId()]);
        }

        return $this->render('chaton/supprimer.html.twig', [
            "chaton" => $chaton,
            'formulaire' => $form->createView(),
        ]);
    }

    public function gererLaPhoto(FormInterface $form, Chaton $chaton){
        $urlPhoto = $form->get('urlPhoto')->getData();
        if($urlPhoto){
            //si le chaton avait déjà une photo, je la supprime
            $this->supprimerLaPhoto($chaton);

            //je récupère le nom original du fichier pour le stocker dans le dossier Photos
            $nomOriginal = pathinfo($urlPhoto->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = $urlPhoto->getClientOriginalExtension();

            //je crée un nom unique pour le fichier (c'est moche mais ça marche)
            $nomUnique = $nomOriginal . '_' . uniqid() . '.' . $extension;

            $urlPhoto->move('Photos', $nomUnique);
            //je remplis ce champ à la main puisque j'avais
            //mis mapped=false dans ChatonType
            $chaton->setUrlPhoto("Photos/".$nomUnique);
        }
    }

    public function supprimerLaPhoto(Chaton $chaton){
        $urlPhoto = $chaton->getUrlPhoto();
        if($urlPhoto){
            $fs = new Filesystem();
            if($fs->exists($urlPhoto)){
                $fs->remove($urlPhoto);
            }
        }
    }
}
