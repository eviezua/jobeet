<?php

namespace App\Tests\Controller;

use App\Factory\UserFactory;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Foundry\Test\ResetDatabase;

/**
  * @group login
  * @group controller
 **/
class LoginControllerTest extends WebTestCase
{
    use ResetDatabase;

    public function testSecurity(): void
    {
        $client = static::createClient();
        $client->request('', '/login');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Please sign in');

        UserFactory::createOne(['username' => 'test_user']);
        $userRepository = static::getContainer()->get(UserRepository::class);

        $testUser = $userRepository->findOneByUsername('test_user');

        $client->loginUser($testUser);
        $client->request('', '/login');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('div', 'Log Out');

        $client->request('', '/logout');
        $client->request('GET', '/');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('div', 'Log In');
    }
}
