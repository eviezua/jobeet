<?php

namespace App\Tests\Entity;

use App\Entity\Affiliate;
use App\Entity\Category;
use App\Entity\Job;
use DateTime;
use PHPUnit\Framework\TestCase;

/**
 * @group Entity
 * @group job
 */
class JobEntityTest extends TestCase
{
    public function testJobEntity(): void
    {
        $category = new Category();
        $category->setName('test category');

        $affiliate = new Affiliate();
        $affiliate->setEmail('test affiliate');

        $job = new Job();
        $job->setCategory($category);
        $job->setType('test');
        $job->setCompany('Test');
        $job->setCreatedAt(new DateTime('2024-12-04'));
        $job->setEmail('test@test.com');
        $job->setUrl('test');
        $job->setPosition('Test');
        $job->setLocation('test, test');
        $job->setDescription(
            'test description'
        );
        $job->setHowToApply('Test');
        $job->setPublic(true);
        $job->setActivated(true);

        $this->assertEquals('test category', $job->getCategory()->getName());
        $this->assertEquals('test', $job->getType());
        $this->assertEquals('Test', $job->getCompany());
        $this->assertEquals('2024-12-04', $job->getCreatedAt()->format('Y-m-d'));
        $this->assertEquals('test@test.com', $job->getEmail());
        $this->assertEquals('test', $job->getUrl());
        $this->assertEquals('Test', $job->getPosition());
        $this->assertEquals('test, test', $job->getLocation());
        $this->assertEquals('test description', $job->getDescription());
        $this->assertEquals('Test', $job->getHowToApply());
        $this->assertTrue($job->isPublic());
        $this->assertTrue($job->isActivated());
    }
}
