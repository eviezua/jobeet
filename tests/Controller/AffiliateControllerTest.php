<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * @group affiliate
 * @group controller
 **/
class AffiliateControllerTest extends WebTestCase
{
    public function testCreate(): void
    {
        $client = static::createClient();
        $client->request('GET', '/affiliate');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Become an Affiliate');
    }
}
