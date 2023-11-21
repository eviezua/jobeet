<?php

namespace App\Controller;

use App\Entity\Category;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

class CategoryController extends AbstractController
{
    #[Route('/category/{slug}', name: 'category.show', methods: ['GET'])]
    public function show(Category $category, Environment $twig): Response
    {
        return new Response($twig->render('category/show.html.twig', [
            'category' => $category,
        ]));
    }
}
