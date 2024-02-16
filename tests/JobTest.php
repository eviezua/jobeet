<?php
use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Affiliate;
use App\Entity\Job;
use App\Factory\AffiliateFactory;
use App\Factory\CategoryFactory;
use App\Factory\JobFactory;
use Doctrine\ORM\EntityManagerInterface;
use Zenstruck\Foundry\Test\ResetDatabase;
use Zenstruck\Foundry\Test\Factories;

class JobTest extends ApiTestCase
{
    use ResetDatabase, Factories;
    private function getEntityManager(): EntityManagerInterface
    {
        return self::getContainer()->get(EntityManagerInterface::class);
    }

    public function testGetCollectionOfJobsWithDifferentAffiliates(): void
    {
        $activeAffiliateProxy = AffiliateFactory::createOne(['active' => true]);
        $activeAffiliate = $activeAffiliateProxy->object();
        $inactiveAffiliateProxy = AffiliateFactory::createOne(['active' => false]);
        $inactiveAffiliate = $inactiveAffiliateProxy->object();
        $activeAffiliateId = $activeAffiliate->getId();
        $inactiveAffiliateId = $inactiveAffiliate->getId();

        $category1 = CategoryFactory::createOne();
        $category1->addAffility(static::getContainer()->get('doctrine')->getRepository(Affiliate::class)->findOneBy(
            ['id' => $activeAffiliateId]));

        $category2 = CategoryFactory::createOne();
        $category2->addAffility(static::getContainer()->get('doctrine')->getRepository(Affiliate::class)->findOneBy(
            ['id' => $inactiveAffiliateId]));

        $category3 = CategoryFactory::createOne();
        $category3->addAffility(static::getContainer()->get('doctrine')->getRepository(Affiliate::class)->findOneBy(
            ['id' => $activeAffiliateId]));
        $category3->addAffility(static::getContainer()->get('doctrine')->getRepository(Affiliate::class)->findOneBy(
            ['id' => $inactiveAffiliateId]));

        $category4 = CategoryFactory::createOne();

        JobFactory::createOne(['category' => $category1, 'activated' => true, 'public' => true]);
        JobFactory::createOne(['category' => $category2, 'activated' => true, 'public' => true]);
        JobFactory::createOne(['category' => $category3, 'activated' => true, 'public' => true]);
        JobFactory::createOne(['category' => $category4, 'activated' => true, 'public' => true]);

        static::createClient()->request('GET', '/api/jobs');

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@context' => '/api/contexts/Job',
            '@id' => '/api/jobs',
            '@type' => 'hydra:Collection',
            'hydra:totalItems' => 2,
        ]);
    }
    public function testGetCollectionOfJobsWithInactiveAffiliates(): void
    {
        $inactiveAffiliateProxy = AffiliateFactory::createOne(['active' => false]);
        $inactiveAffiliate = $inactiveAffiliateProxy->object();
        $inactiveAffiliateId = $inactiveAffiliate->getId();

        $category1 = CategoryFactory::createOne();
        $category1->addAffility(static::getContainer()->get('doctrine')->getRepository(Affiliate::class)->findOneBy(
            ['id' => $inactiveAffiliateId]));


        JobFactory::createOne(['category' => $category1, 'activated' => true, 'public' => false]);
        JobFactory::createOne(['category' => $category1, 'activated' => false, 'public' => true]);
        JobFactory::createOne(['category' => $category1, 'activated' => false, 'public' => false]);

        static::createClient()->request('GET', '/api/jobs');

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@context' => '/api/contexts/Job',
            '@id' => '/api/jobs',
            '@type' => 'hydra:Collection',
            'hydra:totalItems' => 0,
        ]);
    }
    public function testGetCollectionOfJobsWithActivatedAndNotActivatedJobs(): void
    {
        $activeAffiliateProxy = AffiliateFactory::createOne(['active' => true]);
        $activeAffiliate = $activeAffiliateProxy->object();
        $activeAffiliateId = $activeAffiliate->getId();

        $category1 = CategoryFactory::createOne();
        $category1->addAffility(static::getContainer()->get('doctrine')->getRepository(Affiliate::class)->findOneBy(
            ['id' => $activeAffiliateId]));


        JobFactory::createOne(['category' => $category1, 'activated' => true, 'public' => true]);
        JobFactory::createOne(['category' => $category1, 'activated' => false, 'public' => true]);
        JobFactory::createOne(['category' => $category1, 'activated' => false, 'public' => false]);

        static::createClient()->request('GET', '/api/jobs');

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@context' => '/api/contexts/Job',
            '@id' => '/api/jobs',
            '@type' => 'hydra:Collection',
            'hydra:totalItems' => 1,
        ]);
    }
    public function testGetCollectionOfJobsWithPublicAndNotPublicJobs(): void
    {
        $activeAffiliateProxy = AffiliateFactory::createOne(['active' => true]);
        $activeAffiliate = $activeAffiliateProxy->object();
        $activeAffiliateId = $activeAffiliate->getId();

        $category1 = CategoryFactory::createOne();
        $category1->addAffility(static::getContainer()->get('doctrine')->getRepository(Affiliate::class)->findOneBy(
            ['id' => $activeAffiliateId]));

        $category2 = CategoryFactory::createOne();
        $category2->addAffility(static::getContainer()->get('doctrine')->getRepository(Affiliate::class)->findOneBy(
            ['id' => $activeAffiliateId]));


        JobFactory::createOne(['category' => $category1, 'activated' => true, 'public' => true]);
        JobFactory::createOne(['category' => $category2, 'activated' => true, 'public' => false]);
        JobFactory::createOne(['category' => $category2, 'activated' => false, 'public' => false]);

        static::createClient()->request('GET', '/api/jobs');

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@context' => '/api/contexts/Job',
            '@id' => '/api/jobs',
            '@type' => 'hydra:Collection',
            'hydra:totalItems' => 1,
        ]);
    }
    public function testGetJobWithActiveAffiliateAndActivatedAndPublished(): void
    {
        $activeAffiliateProxy = AffiliateFactory::createOne(['active' => true]);
        $activeAffiliate = $activeAffiliateProxy->object();
        $activeAffiliateId = $activeAffiliate->getId();

        $category = CategoryFactory::createOne();
        $category->addAffility(static::getContainer()->get('doctrine')->getRepository(Affiliate::class)->findOneBy(
            ['id' => $activeAffiliateId]));

        $job = JobFactory::createOne(['category' => $category, 'activated' => true, 'public' => true]);
        $jobId = $job->getId();

        static::createClient()->request('GET', "/api/jobs/$jobId");

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@type' => 'Job',
            '@context' => '/api/contexts/Job',
            '@id' => "/api/jobs/$jobId",
        ]);
        $this->assertJsonContains([
            'category' =>  "/api/categories/{$category->getId()}"
        ]);
    }
    public function testGetJobWithInactiveAffiliateAndActivatedAndPublished(): void
    {
        $inactiveAffiliateProxy = AffiliateFactory::createOne(['active' => false]);
        $inactiveAffiliate = $inactiveAffiliateProxy->object();
        $inactiveAffiliateId = $inactiveAffiliate->getId();

        $category = CategoryFactory::createOne();
        $category->addAffility(static::getContainer()->get('doctrine')->getRepository(Affiliate::class)->findOneBy(
            ['id' => $inactiveAffiliateId]));

        $job = JobFactory::createOne(['category' => $category, 'activated' => true, 'public' => true]);
        $jobId = $job->getId();

        static::createClient()->request('GET', "/api/jobs/$jobId");

        $this->assertResponseStatusCodeSame(404);
    }
    public function testGetJobWithActiveAffiliateAndNotActivatedAndPublicJob(): void
    {
        $activeAffiliateProxy = AffiliateFactory::createOne(['active' => true]);
        $activeAffiliate = $activeAffiliateProxy->object();
        $activeAffiliateId = $activeAffiliate->getId();

        $category = CategoryFactory::createOne();
        $category->addAffility(static::getContainer()->get('doctrine')->getRepository(Affiliate::class)->findOneBy(
            ['id' => $activeAffiliateId]));

        $job = JobFactory::createOne(['category' => $category, 'activated' => false, 'public' => true]);
        $jobId = $job->getId();

        static::createClient()->request('GET', "/api/jobs/$jobId");

        $this->assertResponseStatusCodeSame(404);
    }
    public function testGetJobWithActiveAffiliateAndNotPublicJob(): void
    {
        $activeAffiliateProxy = AffiliateFactory::createOne(['active' => true]);
        $activeAffiliate = $activeAffiliateProxy->object();
        $activeAffiliateId = $activeAffiliate->getId();

        $category = CategoryFactory::createOne();
        $category->addAffility(static::getContainer()->get('doctrine')->getRepository(Affiliate::class)->findOneBy(
            ['id' => $activeAffiliateId]));

        $job = JobFactory::createOne(['category' => $category, 'activated' => true, 'public' => false]);
        $jobId = $job->getId();

        static::createClient()->request('GET', "/api/jobs/$jobId");

        $this->assertResponseStatusCodeSame(404);
    }
    public function testGetJobWithActiveAffiliateAndNotPublicAndNotActivatedJob(): void
    {
        $activeAffiliateProxy = AffiliateFactory::createOne(['active' => true]);
        $activeAffiliate = $activeAffiliateProxy->object();
        $activeAffiliateId = $activeAffiliate->getId();

        $category = CategoryFactory::createOne();
        $category->addAffility(static::getContainer()->get('doctrine')->getRepository(Affiliate::class)->findOneBy(
            ['id' => $activeAffiliateId]));

        $job = JobFactory::createOne(['category' => $category, 'activated' => false, 'public' => false]);
        $jobId = $job->getId();

        static::createClient()->request('GET', "/api/jobs/$jobId");

        $this->assertResponseStatusCodeSame(404);
    }
    public function testGetJobWithInactiveAffiliateAndNotPublicAndNotActivatedJob(): void
    {
        $inactiveAffiliateProxy = AffiliateFactory::createOne(['active' => false]);
        $inactiveAffiliate = $inactiveAffiliateProxy->object();
        $inactiveAffiliateId = $inactiveAffiliate->getId();

        $category = CategoryFactory::createOne();
        $category->addAffility(static::getContainer()->get('doctrine')->getRepository(Affiliate::class)->findOneBy(
            ['id' => $inactiveAffiliateId]));

        $job = JobFactory::createOne(['category' => $category, 'activated' => false, 'public' => false]);
        $jobId = $job->getId();

        static::createClient()->request('GET', "/api/jobs/$jobId");

        $this->assertResponseStatusCodeSame(404);
    }
    public function testGetJobWithInactiveAffiliateAndNotPublicAndActivatedJob(): void
    {
        $inactiveAffiliateProxy = AffiliateFactory::createOne(['active' => false]);
        $inactiveAffiliate = $inactiveAffiliateProxy->object();
        $inactiveAffiliateId = $inactiveAffiliate->getId();

        $category = CategoryFactory::createOne();
        $category->addAffility(static::getContainer()->get('doctrine')->getRepository(Affiliate::class)->findOneBy(
            ['id' => $inactiveAffiliateId]));

        $job = JobFactory::createOne(['category' => $category, 'activated' => true, 'public' => false]);
        $jobId = $job->getId();

        static::createClient()->request('GET', "/api/jobs/$jobId");

        $this->assertResponseStatusCodeSame(404);
    }
    public function testGetJobWithInactiveAffiliateAndPublicAndNotActivatedJob(): void
    {
        $inactiveAffiliateProxy = AffiliateFactory::createOne(['active' => false]);
        $inactiveAffiliate = $inactiveAffiliateProxy->object();
        $inactiveAffiliateId = $inactiveAffiliate->getId();

        $category = CategoryFactory::createOne();
        $category->addAffility(static::getContainer()->get('doctrine')->getRepository(Affiliate::class)->findOneBy(
            ['id' => $inactiveAffiliateId]));

        $job = JobFactory::createOne(['category' => $category, 'activated' => false, 'public' => true]);
        $jobId = $job->getId();

        static::createClient()->request('GET', "/api/jobs/$jobId");

        $this->assertResponseStatusCodeSame(404);
    }
}
