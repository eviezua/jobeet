<?php

namespace App\Controller;

use App\Entity\Category;
use App\Repository\JobRepository;
use App\Service\JobHistoryService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

class CategoryController extends AbstractController
{
    #[Route('/category/{slug}', name: 'category.show', methods: ['GET'])]
    public function show(
        Request $request,
        Category $category,
        Environment $twig,
        JobRepository $jobRepository,
        JobHistoryService $jobHistoryService
    ): Response {
        $offset = max(0, $request->query->getInt('offset', 0));
        $paginator = $jobRepository->getPaginatedActiveJobsByCategoryQuery($category, $offset);

        return new Response($twig->render('category/show.html.twig', [
            'category' => $category,
            'activeJobs' => $paginator,
            'historyJobs' => $jobHistoryService->getJobs(),
            'previous' => $offset - JobRepository::PAGINATOR_PER_PAGE,
            'next' => min(count($paginator), $offset + JobRepository::PAGINATOR_PER_PAGE),
        ]));
    }
}
