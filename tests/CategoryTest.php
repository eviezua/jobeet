<?php
namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Affiliate;
use App\Entity\User;
use App\Factory\AffiliateFactory;
use App\Factory\CategoryFactory;
use Doctrine\ORM\EntityManagerInterface;
use Zenstruck\Foundry\Test\ResetDatabase;
use Zenstruck\Foundry\Test\Factories;

class CategoryTest extends ApiTestCase
{
    use ResetDatabase, Factories;
    private function getEntityManager(): EntityManagerInterface
    {
        return self::getContainer()->get(EntityManagerInterface::class);
    }

    public function testGetCollectionOfCategories(): void
    {
        $this->createUser('test','password');
        $token = $this->getToken('test', 'password');

        $category1 = CategoryFactory::createOne();
        $category2 = CategoryFactory::createOne();
        $category3 = CategoryFactory::createOne();
        $category4 = CategoryFactory::createOne();

        $activeAffiliateProxy = AffiliateFactory::createOne(['active' => true]);
        $activeAffiliate = $activeAffiliateProxy->object();
        $inactiveAffiliateProxy = AffiliateFactory::createOne(['active' => false]);
        $inactiveAffiliate = $inactiveAffiliateProxy->object();
        $activeAffiliateId = $activeAffiliate->getId();
        $inactiveAffiliateId = $inactiveAffiliate->getId();

        $category1->addAffility(static::getContainer()->get('doctrine')->getRepository(Affiliate::class)->findOneBy(
            ['id' => $activeAffiliateId]));
        $category2->addAffility(static::getContainer()->get('doctrine')->getRepository(Affiliate::class)->findOneBy(
            ['id' => $inactiveAffiliateId]));
        $category3->addAffility(static::getContainer()->get('doctrine')->getRepository(Affiliate::class)->findOneBy(
            ['id' => $activeAffiliateId]));
        $category4->addAffility(static::getContainer()->get('doctrine')->getRepository(Affiliate::class)->findOneBy(
            ['id' => $inactiveAffiliateId]));

        static::createClient()->request('GET', '/api/categories', ['auth_bearer' => $token]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@context' => '/api/contexts/Category',
            '@id' => '/api/categories',
            '@type' => 'hydra:Collection',
            'hydra:totalItems' => 4,
        ]);
    }
public function testGetCategoryWithNoAffiliates(): void
{
    $this->createUser('test','password');
    $token = $this->getToken('test', 'password');

    $category = CategoryFactory::createOne();

    $categoryId = $category->getId();

    static::createClient()->request('GET', "/api/categories/$categoryId", ['auth_bearer' => $token]);

    $this->assertResponseIsSuccessful();
    $this->assertJsonContains([
        '@type' => 'Category',
        '@context' => '/api/contexts/Category',
        '@id' => "/api/categories/$categoryId",
    ]);
    $this->assertJsonContains([
        'affilities' => [
        ]
    ]);
}

    public function testGetCategoryWithActiveAffiliate(): void
    {
        $this->createUser('test','password');
        $token = $this->getToken('test', 'password');

        $category = CategoryFactory::createOne();

        $activeAffiliateProxy = AffiliateFactory::createOne(['active' => true]);
        $activeAffiliate = $activeAffiliateProxy->object();

        $category->addAffility($activeAffiliate);

        $this->getEntityManager()->flush();

        $categoryId = $category->getId();

        static::createClient()->request('GET', "/api/categories/$categoryId", ['auth_bearer' => $token]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@type' => 'Category',
            '@context' => '/api/contexts/Category',
            '@id' => "/api/categories/$categoryId",
        ]);
        $this->assertJsonContains([
            'affilities' => [
                ['@id' => "/api/affiliates/{$activeAffiliate->getId()}"]
            ]
        ]);
    }
    public function testGetCategoryWithInactiveAffiliate(): void
    {
        $this->createUser('test','password');
        $token = $this->getToken('test', 'password');

        $category = CategoryFactory::createOne();

        $inactiveAffiliateProxy = AffiliateFactory::createOne(['active' => false]);
        $inactiveAffiliate = $inactiveAffiliateProxy->object();

        $category->addAffility($inactiveAffiliate);

        $this->getEntityManager()->flush();

        $categoryId = $category->getId();

        static::createClient()->request('GET', "/api/categories/$categoryId", ['auth_bearer' => $token]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@type' => 'Category',
            '@context' => '/api/contexts/Category',
            '@id' => "/api/categories/$categoryId",
        ]);
        $this->assertJsonContains([
            'affilities' => [
            ]
        ]);
    }
    public function testGetCategoryWithActiveAndInactiveAffiliate(): void
    {
        $this->createUser('test','password');
        $token = $this->getToken('test', 'password');

        $category = CategoryFactory::createOne();

        $activeAffiliateProxy = AffiliateFactory::createOne(['active' => true]);
        $inactiveAffiliateProxy = AffiliateFactory::createOne(['active' => false]);
        $activeAffiliate = $activeAffiliateProxy->object();
        $inactiveAffiliate = $inactiveAffiliateProxy->object();

        $category->addAffility($activeAffiliate);
        $category->addAffility($inactiveAffiliate);

        $this->getEntityManager()->flush();

        $categoryId = $category->getId();

        static::createClient()->request('GET', "/api/categories/$categoryId", ['auth_bearer' => $token]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@type' => 'Category',
            '@context' => '/api/contexts/Category',
            '@id' => "/api/categories/$categoryId",
        ]);
        $this->assertJsonContains([
            'affilities' => [
                ['@id' => "/api/affiliates/{$activeAffiliate->getId()}"]
            ]
        ]);
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
