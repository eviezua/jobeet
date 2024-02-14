<?php
namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Affiliate;
use App\Factory\AffiliateFactory;
use Zenstruck\Browser\HttpOptions;
use Zenstruck\Browser\Json;
use Zenstruck\Foundry\Test\ResetDatabase;
use Zenstruck\Foundry\Test\Factories;

class AffiliateTest extends ApiTestCase
{
    use ResetDatabase, Factories;

    public function testGetCollectionOfActiveAffiliates(): void
    {
        AffiliateFactory::createMany(100, ['active' => true]);
        AffiliateFactory::createMany(100, ['active' => false]);

        static::createClient()->request('GET', '/api/affiliates');

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@context' => '/api/contexts/Affiliate',
            '@id' => '/api/affiliates',
            '@type' => 'hydra:Collection',
            'hydra:totalItems' => 100,
            'hydra:view' => [
                '@id' => '/api/affiliates?page=1',
                '@type' => 'hydra:PartialCollectionView',
                'hydra:first' => '/api/affiliates?page=1',
                'hydra:last' => '/api/affiliates?page=4',
                'hydra:next' => '/api/affiliates?page=2',
            ],
        ]);
    }

    public function testGetActiveAffiliate(): void
    {
        $activeAffiliate = AffiliateFactory::createOne(['active' => true]);
        $affiliate = AffiliateFactory::createOne(['active' => false]);
        $activeAffiliateId = $activeAffiliate->getId();

        static::createClient()->request('GET', "/api/affiliates/$activeAffiliateId");

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@context' => '/api/contexts/Affiliate',
            '@id' => "/api/affiliates/$activeAffiliateId",
            '@type' => 'Affiliate',
        ]);
    }

    public function testGetInactiveAffiliate(): void
    {
        $activeAffiliate = AffiliateFactory::createOne(['active' => true]);
        $affiliate = AffiliateFactory::createOne(['active' => false]);
        $affiliateId = $affiliate->getId();

        static::createClient()->request('GET', "/api/affiliates/$affiliateId");

        $this->assertResponseStatusCodeSame(404);
        $this->assertInstanceOf(
            Affiliate::class,
            static::getContainer()->get('doctrine')->getRepository(Affiliate::class)->findOneBy(
                ['id' => $affiliateId]
            )
        );
    }

}
