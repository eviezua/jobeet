<?php

namespace App\Tests\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Foundry\Test\ResetDatabase;

/**
 * @group registration
 * @group controller
 **/
class RegistrationControllerTest extends WebTestCase
{
    use ResetDatabase;
    public function testRegistration(): void
    {
        $client = static::createClient();
        $client->request('GET', '/register');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Register');

        $client->submitForm('Register', [
            'registration_form[username]' => 'test_user001',
            'registration_form[plainPassword]' => 'Testing',
            'registration_form[agreeTerms]' => true,
        ]);

        $this->assertIsObject(
            static::getContainer()->get('doctrine')
                ->getRepository(User::class)
                ->findOneBy(
                    ['username' => 'test_user001']
                )
        );
    }
}
