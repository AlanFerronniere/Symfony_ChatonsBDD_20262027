<?php

namespace App\Controller;

use App\Repository\CategorieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class MenuController extends AbstractController
{
    public function _menu(CategorieRepository $repository): Response
    {
        $categories=$repository->findAll();

        return $this->render('menu/_menu.html.twig', [
            "categories" => $categories,
        ]);
    }
}
