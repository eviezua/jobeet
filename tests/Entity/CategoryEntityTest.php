<?php

namespace App\Tests\Entity;

use App\Entity\Category;
use PHPUnit\Framework\TestCase;

/**
 * @group Entity
 * @group category
 */
class CategoryEntityTest extends TestCase
{
    public function testCategoryEntity(): void
    {
        $category = new Category();
        $category->setName('Test category');
        $category->setSlug('test-category');

        $this->assertEquals('Test category', $category->getName());
        $this->assertEquals('test-category', $category->getSlug());
    }
}
