<?php
namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Affiliate;
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
        $category3->addAffility(static::getContainer()->get('doctrine')->getRepository(Affiliate::class)->findOneBy(
            ['id' => $inactiveAffiliateId]));

        static::createClient()->request('GET', '/api/categories');

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

    $category = CategoryFactory::createOne();

    $categoryId = $category->getId();

    static::createClient()->request('GET', "/api/categories/$categoryId");

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

        $category = CategoryFactory::createOne();

        $activeAffiliateProxy = AffiliateFactory::createOne(['active' => true]);
        $activeAffiliate = $activeAffiliateProxy->object();

        $category->addAffility($activeAffiliate);

        $this->getEntityManager()->flush();

        $categoryId = $category->getId();

        static::createClient()->request('GET', "/api/categories/$categoryId");

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

        $category = CategoryFactory::createOne();

        $inactiveAffiliateProxy = AffiliateFactory::createOne(['active' => false]);
        $inactiveAffiliate = $inactiveAffiliateProxy->object();

        $category->addAffility($inactiveAffiliate);

        $this->getEntityManager()->flush();

        $categoryId = $category->getId();

        static::createClient()->request('GET', "/api/categories/$categoryId");

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

        $category = CategoryFactory::createOne();

        $activeAffiliateProxy = AffiliateFactory::createOne(['active' => true]);
        $inactiveAffiliateProxy = AffiliateFactory::createOne(['active' => false]);
        $activeAffiliate = $activeAffiliateProxy->object();
        $inactiveAffiliate = $inactiveAffiliateProxy->object();

        $category->addAffility($activeAffiliate);
        $category->addAffility($inactiveAffiliate);

        $this->getEntityManager()->flush();

        $categoryId = $category->getId();

        static::createClient()->request('GET', "/api/categories/$categoryId");

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
}
