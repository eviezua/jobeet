<?php

namespace App\Tests\Command;

use Proxies\__CG__\App\Entity\Category;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Zenstruck\Foundry\Test\ResetDatabase;

/**
 * @group Command
 * @group category
 */
class CreateCategoryTest extends KernelTestCase
{
    use ResetDatabase;

    public function testExecute(): void
    {
        self::bootKernel();
        $application = new Application(self::$kernel);

        $command = $application->find('app:create-category');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'name' => 'New Category!',
        ]);

        $commandTester->assertCommandIsSuccessful();

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('Category successfully created!', $output);
        $this->assertEquals(
            static::getContainer()->get('doctrine')->getRepository(Category::class)->findOneBy(
                ['slug' => 'new-category']
            )->getName(),
            'New Category!'
        );
    }
}
