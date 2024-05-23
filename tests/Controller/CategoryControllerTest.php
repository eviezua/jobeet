<?php

namespace App\Tests\Controller;

use App\Factory\CategoryFactory;
use App\Factory\JobFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Browser\Test\HasBrowser;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

/**
 * @group category
 * @group controller
 * @group my
 **/
class CategoryControllerTest extends WebTestCase
{
    use Factories;
    use HasBrowser;
    use ResetDatabase;
    public function testShow(): void
    {
        $client = static::createClient();
        $category = CategoryFactory::createOne(['slug' => 'test']);
        JobFactory::createMany(100, ['category' => $category, 'activated' => true, 'public' => true, 'expires_at' => new \DateTime('+ 1 day')]);
        $client->request('GET', '/category/test');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h4', $category->getName());
        $this->assertSelectorTextContains('', 'Next');
        $client->clickLink('Next');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('', 'Previous');
        $this->assertSelectorTextContains('', 'Next');

    }
}
