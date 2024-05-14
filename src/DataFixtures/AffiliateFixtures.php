<?php

namespace App\DataFixtures;

use App\Entity\Affiliate;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class AffiliateFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $affiliateSensioLabs = new Affiliate();
        $affiliateSensioLabs->setUrl('http://www.sensiolabs.com/');
        $affiliateSensioLabs->setEmail('contact@sensiolabs.com');
        $affiliateSensioLabs->setActive(true);
        $affiliateSensioLabs->setToken('sensio_labs');
        $affiliateSensioLabs->addCategory($manager->merge($this->getReference('category-programming')));

        $affiliateKNPLabs = new Affiliate();
        $affiliateKNPLabs->setUrl('http://www.knplabs.com/');
        $affiliateKNPLabs->setEmail('hello@knplabs.com');
        $affiliateKNPLabs->setActive(true);
        $affiliateKNPLabs->setToken('knp_labs');
        $affiliateKNPLabs->addCategory($manager->merge($this->getReference('category-programming')));
        $affiliateKNPLabs->addCategory($manager->merge($this->getReference('category-design')));

        $affiliateprivat = new Affiliate();
        $affiliateprivat->setUrl('http://www.privat-bank.com/');
        $affiliateprivat->setEmail('privat-bank@gmail.com');
        $affiliateprivat->setActive(false);
        $affiliateprivat->setToken('privat_bank');
        $affiliateprivat->addCategory($manager->merge($this->getReference('category-administrator')));
        $affiliateprivat->addCategory($manager->merge($this->getReference('category-programming')));

        $manager->persist($affiliateSensioLabs);
        $manager->persist($affiliateKNPLabs);
        $manager->persist($affiliateprivat);

        $manager->flush();

        $this->addReference('sensio_labs', $affiliateSensioLabs);
        $this->addReference('knp_labs', $affiliateKNPLabs);
        $this->addReference('privat_bank', $affiliateprivat);
    }

    public function getDependencies(): array
    {
        return [
            CategoryFixtures::class,
        ];
    }
}
