<?php

namespace App\Tests\Controller\Admin;

use App\Controller\Admin\CategoryController;
use App\Controller\Admin\JobCrudController;
use App\Factory\UserFactory;
use App\Repository\UserRepository;
use EasyCorp\Bundle\EasyAdminBundle\Test\AbstractCrudTestCase;
use Zenstruck\Foundry\Test\ResetDatabase;

/**
 * @group admin
 * @group controller
 * @group job
 **/
final class JobCrudControllerTest extends AbstractCrudTestCase
{
    use ResetDatabase;
    protected function getControllerFqcn(): string
    {
        return JobCrudController::class;
    }

    protected function getDashboardFqcn(): string
    {
        return CategoryController::class;
    }
    public function testIndexPage(): void
    {
        UserFactory::createOne(['username' => 'test_admin', 'roles' => ['ROLE_ADMIN']]);
        $userRepository = static::getContainer()->get(UserRepository::class);

        $testAdmin = $userRepository->findOneByUsername('test_admin');

        $this->client->loginUser($testAdmin);

        $this->client->request("GET", $this->generateIndexUrl());
        $this->assertResponseIsSuccessful();
    }
}
