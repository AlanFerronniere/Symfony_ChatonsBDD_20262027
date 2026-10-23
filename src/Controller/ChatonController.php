<?php

namespace App\Controller;

use App\Entity\Chaton;
use App\Form\ChatonType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class ChatonController extends AbstractController
{
    #[Route('/chaton', name: 'app_chaton')]
    public function index(): Response
    {
        return $this->render('chaton/index.html.twig', [
            'controller_name' => 'ChatonController',
        ]);
    }

    #[Route('/ajouter_chaton', name: 'app_ajouter_chaton')]
    public function ajouterChaton(Request $request): Response
    {
        //je crée un chaton vide
        $chaton = new Chaton();
        //un formulaire
        $form = $this->createForm(ChatonType::class, $chaton);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            //todo A faire...
        }

        return $this->render('chaton/ajouter.html.twig', [
            'formulaire' => $form->createView(),
        ]);
    }
}
