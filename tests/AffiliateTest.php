<?php
namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Affiliate;
use App\Entity\User;
use App\Factory\AffiliateFactory;
use Zenstruck\Foundry\Test\ResetDatabase;
use Zenstruck\Foundry\Test\Factories;

class AffiliateTest extends ApiTestCase
{
    use ResetDatabase, Factories;
    public function testGetCollectionOfActiveAffiliates(): void
    {
        $this->createUser('test','password');
        $token = $this->getToken('test', 'password');

        AffiliateFactory::createMany(100, ['active' => true]);
        AffiliateFactory::createMany(100, ['active' => false]);

        static::createClient()->request('GET', '/api/affiliates', ['auth_bearer' => $token]);

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
        $this->createUser('test','password');
        $token = $this->getToken('test', 'password');

        $activeAffiliate = AffiliateFactory::createOne(['active' => true]);
        $activeAffiliateId = $activeAffiliate->getId();

        static::createClient()->request('GET', "/api/affiliates/$activeAffiliateId", ['auth_bearer' => $token]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@context' => '/api/contexts/Affiliate',
            '@id' => "/api/affiliates/$activeAffiliateId",
            '@type' => 'Affiliate',
        ]);
    }
    public function testGetInactiveAffiliate(): void
    {
        $this->createUser('test','password');
        $token = $this->getToken('test', 'password');

        $affiliate = AffiliateFactory::createOne(['active' => false]);
        $affiliateId = $affiliate->getId();

        static::createClient()->request('GET', "/api/affiliates/$affiliateId", ['auth_bearer' => $token]);

        $this->assertResponseStatusCodeSame(404);
        $this->assertInstanceOf(
            Affiliate::class,
            static::getContainer()->get('doctrine')->getRepository(Affiliate::class)->findOneBy(
                ['id' => $affiliateId]
            )
        );
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
