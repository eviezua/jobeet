<?php

namespace App\Tests\Controller;

use App\Entity\Category;
use App\Entity\Job;
use App\Factory\CategoryFactory;
use App\Factory\JobFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Browser\Test\HasBrowser;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

/**
 * @group job
 * @group controller
 **/
class JobControllerTest extends WebTestCase
{
    use Factories;
    use HasBrowser;
    use ResetDatabase;

    public function testJobList(): void
    {
        $client = static::createClient();
        $this->CreateJob(true);
        $client->request('GET', '/');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h4', 'Test Category');
    }

    public function testCreateJob(): void
    {
        $client = static::createClient();

        $category = CategoryFactory::createOne(['name' => 'test']);

        $client->request('GET', "/job/create");

        $client->submitForm('Create', [
            'job[type]' => 'full-time',
            'job[company]' => 'Test',
            'job[position]' => 'Test',
            'job[location]' => 'Test, Test',
            'job[description]' => 'Some feedback from an automated functional test',
            'job[howToApply]' => 'Testing',
            'job[public]' => 1,
            'job[activated]' => 1,
            'job[email]' => 'test@test.com',
            'job[category]' => $category->getId(),
        ]);

        $job = static::getContainer()->get('doctrine')
            ->getRepository(Job::class)
            ->findOneBy(
                ['position' => 'Test']
            );
        $this->assertResponseRedirects("/job/admin/{$job->getToken()}");

        $client->request('GET', '/');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('td', 'Test, Test');
    }

    public function testShowJobById(): void
    {
        $client = static::createClient();
        $job = $this->CreateJob(true);
        $id = $job->getId();
        $client->request('GET', "/job/$id");
        $this->assertResponseIsSuccessful();
    }

    public function testShowJobByToken(): void
    {
        $client = static::createClient();
        $job = $this->CreateJob(true);
        $token = $job->getToken();
        $client->request('GET', "/job/admin/$token");
        $this->assertResponseIsSuccessful();
    }

    public function testDeleteJob(): void
    {
        $client = static::createClient();
        $job = $this->CreateJob(true);
        $token = $job->getToken();
        $client->request('DELETE', "/job/admin/$token/delete");
        $client->request('GET', "/job/admin/$token");
        $this->assertResponseIsSuccessful();
    }

    public function testPublishJobByToken(): void
    {
        $client = static::createClient();

        $job = $this->CreateJob(false);
        $token = $job->getToken();

        $client->request('GET', "/job/admin/$token");
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('', 'Publish');

        $form = $client->getCrawler()->selectButton('Publish')->form();
        $client->submit($form);

        $this->assertResponseRedirects("/job/admin/$token");
        $client->followRedirect();


        $this->assertSelectorTextContains('div[class="alert alert-success"]', 'Your job was published');

        $result = static::getContainer()->get('doctrine')
            ->getRepository(Job::class)
            ->findOneBy(
                ['position' => 'Test Job']
            )->isActivated();
        $this->assertTrue($result);
    }

    public function testEditJobByToken(): void
    {
        $client = static::createClient();
        $job = $this->CreateJob(true);
        $token = $job->getToken();

        $client->request('GET', "/job/admin/$token/edit");
        $client->submitForm('Edit', [
            'job[type]' => 'full-time',
            'job[company]' => 'Test',
            'job[position]' => 'Test',
            'job[location]' => 'Test, Test',
            'job[description]' => 'Some feedback from an automated functional test',
            'job[howToApply]' => 'Testing',
            'job[public]' => 1,
            'job[activated]' => 1,
            'job[email]' => 'test@test.com',
            'job[category]' => 1,
        ]);

        $client->request('GET', '/');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('td', 'Test');
    }

    public function CreateJob($activated)
    {
        CategoryFactory::createOne(['name' => 'Test Category']);

        $job = JobFactory::createOne([
            'position' => 'Test Job',
            'public' => true,
            'activated' => $activated,
            'category' => static::getContainer()->get('doctrine')
                ->getRepository(Category::class)
                ->findOneBy(
                    ['name' => 'Test Category']
                ),
            'expiresAt' => (new \DateTime('+30 day')),
        ]);

        return $job;
    }
}
