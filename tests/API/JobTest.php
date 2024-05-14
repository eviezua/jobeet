<?php

namespace App\Tests\API;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Affiliate;
use App\Entity\User;
use App\Factory\AffiliateFactory;
use App\Factory\CategoryFactory;
use App\Factory\JobFactory;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

/**
 * @group api
 * @group job
 */
class JobTest extends ApiTestCase
{
    use ResetDatabase;
    use Factories;

    public function testGetCollectionOfJobsWithDifferentAffiliates(): void
    {
        $this->createUser('test', 'password');
        $token = $this->getToken('test', 'password');

        $activeAffiliateProxy = AffiliateFactory::createOne(['active' => true]);
        $activeAffiliate = $activeAffiliateProxy->object();
        $inactiveAffiliateProxy = AffiliateFactory::createOne(['active' => false]);
        $inactiveAffiliate = $inactiveAffiliateProxy->object();
        $activeAffiliateId = $activeAffiliate->getId();
        $inactiveAffiliateId = $inactiveAffiliate->getId();

        $category1 = CategoryFactory::createOne();
        $category1->addAffility(
            static::getContainer()->get('doctrine')->getRepository(Affiliate::class)->findOneBy(
                ['id' => $activeAffiliateId]
            )
        );

        $category2 = CategoryFactory::createOne();
        $category2->addAffility(
            static::getContainer()->get('doctrine')->getRepository(Affiliate::class)->findOneBy(
                ['id' => $inactiveAffiliateId]
            )
        );

        $category3 = CategoryFactory::createOne();
        $category3->addAffility(
            static::getContainer()->get('doctrine')->getRepository(Affiliate::class)->findOneBy(
                ['id' => $activeAffiliateId]
            )
        );
        $category3->addAffility(
            static::getContainer()->get('doctrine')->getRepository(Affiliate::class)->findOneBy(
                ['id' => $inactiveAffiliateId]
            )
        );

        $category4 = CategoryFactory::createOne();

        JobFactory::createOne(['category' => $category1, 'activated' => true, 'public' => true]);
        JobFactory::createOne(['category' => $category2, 'activated' => true, 'public' => true]);
        JobFactory::createOne(['category' => $category3, 'activated' => true, 'public' => true]);
        JobFactory::createOne(['category' => $category4, 'activated' => true, 'public' => true]);

        static::createClient()->request('GET', '/api/jobs', ['auth_bearer' => $token]);

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
        $this->createUser('test', 'password');
        $token = $this->getToken('test', 'password');

        $inactiveAffiliateProxy = AffiliateFactory::createOne(['active' => false]);
        $inactiveAffiliate = $inactiveAffiliateProxy->object();
        $inactiveAffiliateId = $inactiveAffiliate->getId();

        $category1 = CategoryFactory::createOne();
        $category1->addAffility(
            static::getContainer()->get('doctrine')->getRepository(Affiliate::class)->findOneBy(
                ['id' => $inactiveAffiliateId]
            )
        );


        JobFactory::createOne(['category' => $category1, 'activated' => true, 'public' => false]);
        JobFactory::createOne(['category' => $category1, 'activated' => false, 'public' => true]);
        JobFactory::createOne(['category' => $category1, 'activated' => false, 'public' => false]);

        static::createClient()->request('GET', '/api/jobs', ['auth_bearer' => $token]);

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
        $this->createUser('test', 'password');
        $token = $this->getToken('test', 'password');

        $activeAffiliateProxy = AffiliateFactory::createOne(['active' => true]);
        $activeAffiliate = $activeAffiliateProxy->object();
        $activeAffiliateId = $activeAffiliate->getId();

        $category1 = CategoryFactory::createOne();
        $category1->addAffility(
            static::getContainer()->get('doctrine')->getRepository(Affiliate::class)->findOneBy(
                ['id' => $activeAffiliateId]
            )
        );


        JobFactory::createOne(['category' => $category1, 'activated' => true, 'public' => true]);
        JobFactory::createOne(['category' => $category1, 'activated' => false, 'public' => true]);
        JobFactory::createOne(['category' => $category1, 'activated' => false, 'public' => false]);

        static::createClient()->request('GET', '/api/jobs', ['auth_bearer' => $token]);

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
        $this->createUser('test', 'password');
        $token = $this->getToken('test', 'password');

        $activeAffiliateProxy = AffiliateFactory::createOne(['active' => true]);
        $activeAffiliate = $activeAffiliateProxy->object();
        $activeAffiliateId = $activeAffiliate->getId();

        $category1 = CategoryFactory::createOne();
        $category1->addAffility(
            static::getContainer()->get('doctrine')->getRepository(Affiliate::class)->findOneBy(
                ['id' => $activeAffiliateId]
            )
        );

        $category2 = CategoryFactory::createOne();
        $category2->addAffility(
            static::getContainer()->get('doctrine')->getRepository(Affiliate::class)->findOneBy(
                ['id' => $activeAffiliateId]
            )
        );


        JobFactory::createOne(['category' => $category1, 'activated' => true, 'public' => true]);
        JobFactory::createOne(['category' => $category2, 'activated' => true, 'public' => false]);
        JobFactory::createOne(['category' => $category2, 'activated' => false, 'public' => false]);

        static::createClient()->request('GET', '/api/jobs', ['auth_bearer' => $token]);

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
        $this->createUser('test', 'password');
        $token = $this->getToken('test', 'password');

        $activeAffiliateProxy = AffiliateFactory::createOne(['active' => true]);
        $activeAffiliate = $activeAffiliateProxy->object();
        $activeAffiliateId = $activeAffiliate->getId();

        $category = CategoryFactory::createOne();
        $category->addAffility(
            static::getContainer()->get('doctrine')->getRepository(Affiliate::class)->findOneBy(
                ['id' => $activeAffiliateId]
            )
        );

        $job = JobFactory::createOne(['category' => $category, 'activated' => true, 'public' => true]);
        $jobId = $job->getId();

        static::createClient()->request('GET', "/api/jobs/$jobId", ['auth_bearer' => $token]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@type' => 'Job',
            '@context' => '/api/contexts/Job',
            '@id' => "/api/jobs/$jobId",
        ]);
        $this->assertJsonContains([
            'category' => "/api/categories/{$category->getId()}",
        ]);
    }

    public function testGetJobWithInactiveAffiliateAndActivatedAndPublished(): void
    {
        $this->createUser('test', 'password');
        $token = $this->getToken('test', 'password');

        $inactiveAffiliateProxy = AffiliateFactory::createOne(['active' => false]);
        $inactiveAffiliate = $inactiveAffiliateProxy->object();
        $inactiveAffiliateId = $inactiveAffiliate->getId();

        $category = CategoryFactory::createOne();
        $category->addAffility(
            static::getContainer()->get('doctrine')->getRepository(Affiliate::class)->findOneBy(
                ['id' => $inactiveAffiliateId]
            )
        );

        $job = JobFactory::createOne(['category' => $category, 'activated' => true, 'public' => true]);
        $jobId = $job->getId();

        static::createClient()->request('GET', "/api/jobs/$jobId", ['auth_bearer' => $token]);

        $this->assertResponseStatusCodeSame(404);
    }

    public function testGetJobWithActiveAffiliateAndNotActivatedAndPublicJob(): void
    {
        $this->createUser('test', 'password');
        $token = $this->getToken('test', 'password');

        $activeAffiliateProxy = AffiliateFactory::createOne(['active' => true]);
        $activeAffiliate = $activeAffiliateProxy->object();
        $activeAffiliateId = $activeAffiliate->getId();

        $category = CategoryFactory::createOne();
        $category->addAffility(
            static::getContainer()->get('doctrine')->getRepository(Affiliate::class)->findOneBy(
                ['id' => $activeAffiliateId]
            )
        );

        $job = JobFactory::createOne(['category' => $category, 'activated' => false, 'public' => true]);
        $jobId = $job->getId();

        static::createClient()->request('GET', "/api/jobs/$jobId", ['auth_bearer' => $token]);

        $this->assertResponseStatusCodeSame(404);
    }

    public function testGetJobWithActiveAffiliateAndNotPublicJob(): void
    {
        $this->createUser('test', 'password');
        $token = $this->getToken('test', 'password');

        $activeAffiliateProxy = AffiliateFactory::createOne(['active' => true]);
        $activeAffiliate = $activeAffiliateProxy->object();
        $activeAffiliateId = $activeAffiliate->getId();

        $category = CategoryFactory::createOne();
        $category->addAffility(
            static::getContainer()->get('doctrine')->getRepository(Affiliate::class)->findOneBy(
                ['id' => $activeAffiliateId]
            )
        );

        $job = JobFactory::createOne(['category' => $category, 'activated' => true, 'public' => false]);
        $jobId = $job->getId();

        static::createClient()->request('GET', "/api/jobs/$jobId", ['auth_bearer' => $token]);

        $this->assertResponseStatusCodeSame(404);
    }

    public function testGetJobWithActiveAffiliateAndNotPublicAndNotActivatedJob(): void
    {
        $this->createUser('test', 'password');
        $token = $this->getToken('test', 'password');

        $activeAffiliateProxy = AffiliateFactory::createOne(['active' => true]);
        $activeAffiliate = $activeAffiliateProxy->object();
        $activeAffiliateId = $activeAffiliate->getId();

        $category = CategoryFactory::createOne();
        $category->addAffility(
            static::getContainer()->get('doctrine')->getRepository(Affiliate::class)->findOneBy(
                ['id' => $activeAffiliateId]
            )
        );

        $job = JobFactory::createOne(['category' => $category, 'activated' => false, 'public' => false]);
        $jobId = $job->getId();

        static::createClient()->request('GET', "/api/jobs/$jobId", ['auth_bearer' => $token]);

        $this->assertResponseStatusCodeSame(404);
    }

    public function testGetJobWithInactiveAffiliateAndNotPublicAndNotActivatedJob(): void
    {
        $this->createUser('test', 'password');
        $token = $this->getToken('test', 'password');

        $inactiveAffiliateProxy = AffiliateFactory::createOne(['active' => false]);
        $inactiveAffiliate = $inactiveAffiliateProxy->object();
        $inactiveAffiliateId = $inactiveAffiliate->getId();

        $category = CategoryFactory::createOne();
        $category->addAffility(
            static::getContainer()->get('doctrine')->getRepository(Affiliate::class)->findOneBy(
                ['id' => $inactiveAffiliateId]
            )
        );

        $job = JobFactory::createOne(['category' => $category, 'activated' => false, 'public' => false]);
        $jobId = $job->getId();

        static::createClient()->request('GET', "/api/jobs/$jobId", ['auth_bearer' => $token]);

        $this->assertResponseStatusCodeSame(404);
    }

    public function testGetJobWithInactiveAffiliateAndNotPublicAndActivatedJob(): void
    {
        $this->createUser('test', 'password');
        $token = $this->getToken('test', 'password');

        $inactiveAffiliateProxy = AffiliateFactory::createOne(['active' => false]);
        $inactiveAffiliate = $inactiveAffiliateProxy->object();
        $inactiveAffiliateId = $inactiveAffiliate->getId();

        $category = CategoryFactory::createOne();
        $category->addAffility(
            static::getContainer()->get('doctrine')->getRepository(Affiliate::class)->findOneBy(
                ['id' => $inactiveAffiliateId]
            )
        );

        $job = JobFactory::createOne(['category' => $category, 'activated' => true, 'public' => false]);
        $jobId = $job->getId();

        static::createClient()->request('GET', "/api/jobs/$jobId", ['auth_bearer' => $token]);

        $this->assertResponseStatusCodeSame(404);
    }

    public function testGetJobWithInactiveAffiliateAndPublicAndNotActivatedJob(): void
    {
        $this->createUser('test', 'password');
        $token = $this->getToken('test', 'password');

        $inactiveAffiliateProxy = AffiliateFactory::createOne(['active' => false]);
        $inactiveAffiliate = $inactiveAffiliateProxy->object();
        $inactiveAffiliateId = $inactiveAffiliate->getId();

        $category = CategoryFactory::createOne();
        $category->addAffility(
            static::getContainer()->get('doctrine')->getRepository(Affiliate::class)->findOneBy(
                ['id' => $inactiveAffiliateId]
            )
        );

        $job = JobFactory::createOne(['category' => $category, 'activated' => false, 'public' => true]);
        $jobId = $job->getId();

        static::createClient()->request('GET', "/api/jobs/$jobId", ['auth_bearer' => $token]);

        $this->assertResponseStatusCodeSame(404);
    }

    protected function createUser(string $username, string $password): User
    {
        $container = self::getContainer();

        $user = new User();
        $user->setUsername($username);
        $user->setPassword(
            $container->get('security.user_password_hasher')->hashPassword($user, $password)
        );

        $manager = $container->get('doctrine')->getManager();
        $manager->persist($user);
        $manager->flush();

        return $user;
    }

    protected function getToken(string $username, string $password): string
    {
        $response = static::createClient()->request('POST', '/auth', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'username' => $username,
                'password' => $password,
            ],
        ]);

        return $response->toArray()['token'];
    }
}
