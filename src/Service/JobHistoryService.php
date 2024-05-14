<?php

namespace App\Service;

use App\Entity\Job;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;


class JobHistoryService
{
    private const MAX = 3;

    private $requestStack;

    private $em;

    public function __construct(RequestStack $requestStack, EntityManagerInterface $em)
    {
        $this->requestStack = $requestStack;
        $this->em = $em;
    }

    public function addJob(Job $job): void
    {
        $jobs = $this->getJobsIds();

        // Add job id to the beginning of the array
        array_unshift($jobs, $job->getId());

        // Remove duplication of ids
        $jobs = array_unique($jobs);

        // Get only first 3 elements
        $jobs = array_slice($jobs, 0, self::MAX);

        // Store IDs in session
        $this->requestStack->getSession()->set('job_history', $jobs);
    }

    public function getJobs(): array
    {
        $jobs = [];
        $jobRepository = $this->em->getRepository(Job::class);

        foreach ($this->getJobsIds() as $jobId) {
            $jobs[] = $jobRepository->findActiveJob($jobId);
        }

        return array_filter($jobs);
    }

    public function getJobsIds(): array
    {
        return $this->requestStack->getSession()->get('job_history', []);
    }
}

