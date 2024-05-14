<?php

namespace App\Tests\Entity;

use App\Entity\Affiliate;
use PHPUnit\Framework\TestCase;

class AffiliateEntityTest extends TestCase
{
    public function testAffiliateEntity(): void
    {
        $affiliate = new Affiliate();
        $affiliate->setEmail('test@test.com');
        $affiliate->setUrl('https://test.com');
        $affiliate->setActive(true);
        $affiliate->setToken('1234567890');

        $this->assertEquals('test@test.com', $affiliate->getEmail());
        $this->assertEquals('https://test.com', $affiliate->getUrl());
        $this->assertTrue($affiliate->isActive());
        $this->assertEquals('1234567890', $affiliate->getToken());
    }
}
