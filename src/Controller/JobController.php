<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Job;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\JobType;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Form\FormInterface;
class JobController extends AbstractController
{
    #[Route('/', name: 'job.list', methods: ['GET', 'POST'])]
    public function list(Environment $twig, EntityManagerInterface $em): Response
    {
        $categories = $em->getRepository(Category::class)->findWithActiveJobs();
        return new Response($twig->render('job/list.html.twig', [
            'categories' => $categories,
        ]));
    }

    #[Route('job/create', name: 'job.create', methods: ['POST', 'GET'])]
    public function create(Request $request, EntityManagerInterface $em, #[Autowire('%jobs_directory%')] string $jobsDir) : Response
    {
        $job = new Job();
        $form = $this->createForm(JobType::class, $job);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($logoFile = $form['logo']->getData()) {
                $fileName = \bin2hex(\random_bytes(10)) . '.' . $logoFile->guessExtension();

                // moves the file to the directory where brochures are stored
                $logoFile->move($jobsDir, $fileName);
                $job->setLogo($fileName);
            }
            $em->persist($job);
            $em->flush();

            /*return $this->redirectToRoute('job.list');*/
            return $this->redirectToRoute(
                'job.preview',
                ['token' => $job->getToken()]
            );

        }

        return $this->render('job/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    #[Route('job/{id}', name: 'job.show', methods: ['GET'])]
    public function show( Environment $twig, Job $job, EntityManagerInterface $em) : Response
    {
        /*$job = $em->getRepository(Job::class)->findActiveJob();*/
        return new Response($twig->render('job/show.html.twig', [
            'job' => $job,
        ]));
    }
    #[Route('job/admin/{token}', name: 'job.preview', methods: ['GET'])]
    public function preview(Job $job) : Response
    {
        $deleteForm = $this->createDeleteForm($job);
        $publishForm = $this->createPublishForm($job);
        return $this->render('job/show.html.twig', [
            'job' => $job,
            'hasControlAccess' => true,
            'deleteForm' => $deleteForm->createView(),
            'publishForm' => $publishForm->createView(),
        ]);
    }
    private function createDeleteForm(Job $job) : FormInterface
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('job.delete', ['token' => $job->getToken()]))
            ->setMethod('DELETE')
            ->getForm();
    }
    #[Route('job/admin/{token}/delete', name: 'job.delete', methods: ['DELETE', 'POST'])]
    public function delete(Request $request, Job $job, EntityManagerInterface $em) : Response
    {
        $form = $this->createDeleteForm($job);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->remove($job);
            $em->flush();
        }

        return $this->redirectToRoute('job.list');
    }
    private function createPublishForm(Job $job) : FormInterface
    {
        return $this->createFormBuilder(['token' => $job->getToken()])
            ->setAction($this->generateUrl('job.publish', ['token' => $job->getToken()]))
            ->setMethod('POST')
            ->getForm();
    }
    #[Route('job/admin/{token}/publish', name: 'job.publish', methods: ['POST'])]
    public function publish(Request $request, Job $job, EntityManagerInterface $em) : Response
    {
        $form = $this->createPublishForm($job);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $job->setActivated(true);

            $em->flush();

            $this->addFlash('notice', 'Your job was published');
        }

        return $this->redirectToRoute('job.preview', [
            'token' => $job->getToken(),
        ]);
    }
    #[Route('job/admin/{token}/edit', name: 'job.edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Job $job, EntityManagerInterface $em) : Response
    {
        $form = $this->createForm(JobType::class, $job);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            /*return $this->redirectToRoute('job.list');*/
            return $this->redirectToRoute(
                'job.preview',
                ['token' => $job->getToken()]
            );

        }

        return $this->render('job/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

}
