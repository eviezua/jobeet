<?php

namespace App\Controller;

use App\Entity\Job;
use App\Repository\JobRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;
class JobController extends AbstractController
{
    #[Route('/', name: 'job.list', methods: ['GET'])]
    public function list(Environment $twig, JobRepository $jobRepository): Response
    {
        return new Response($twig->render('job/list.html.twig', [
            'jobs' => $jobRepository->findAll()
        ]));
    }
    #[Route('job/{id}', name: 'job.show', methods: ['GET'])]
    public function show(Environment $twig, Job $job) : Response
    {
        return new Response($twig->render('job/show.html.twig', [
            'job' => $job,
        ]));
    }
}
