<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Job;
use App\Repository\JobRepository;
use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
class JobController extends AbstractController
{
    #[Route('/', name: 'job.list', methods: ['GET'])]
    public function list(Environment $twig, EntityManagerInterface $em): Response
    {
        $categories = $em->getRepository(Category::class)->findWithActiveJobs();
        return new Response($twig->render('job/list.html.twig', [
            'categories' => $categories,
        ]));
    }
    #[Route('job/{id}', name: 'job.show', methods: ['GET'])]
    public function show( Environment $twig, #[MapEntity(expr: 'repository.findActiveJob(id)')] Job $job) : Response
    {
        return new Response($twig->render('job/show.html.twig', [
            'job' => $job,
        ]));
    }
}
