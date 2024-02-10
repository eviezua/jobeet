<?php

namespace App\DataFixtures;

use App\Entity\Job;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
class JobFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager) : void
    {
        for ($i = 1; $i <= 30; $i++) {
            $jobSensioLabs = new Job();
            $jobSensioLabs->setCategory($manager->merge($this->getReference('category-programming')));
            $jobSensioLabs->setType('full-time');
            $jobSensioLabs->setCompany('Sensio Labs');
            $jobSensioLabs->setLogo('job1.png');
            $jobSensioLabs->setUrl('http://www.sensiolabs.com/');
            $jobSensioLabs->setPosition('Web Developer');
            $jobSensioLabs->setLocation('Paris, France');
            $jobSensioLabs->setDescription('You\'ve already developed websites with symfony and you want to work with Open-Source technologies. You have a minimum of 3 years experience in web development with PHP or Java and you wish to participate to development of Web 2.0 sites using the best frameworks available.');
            $jobSensioLabs->setHowToApply('Send your resume to fabien.potencier [at] sensio.com');
            $jobSensioLabs->setPublic(true);
            $jobSensioLabs->setActivated(true);
            $jobSensioLabs->setEmail('job@example.com');
            $jobSensioLabs->setCreatedAt(new \DateTime('-30 days'));
            $jobSensioLabs->setUpdatedAt(new \DateTime());
            $jobSensioLabs->setExpiresAt(new \DateTime('+30 days'));

            $jobExtremeSensio = new Job();
            $jobExtremeSensio->setCategory($manager->merge($this->getReference('category-design')));
            $jobExtremeSensio->setType('part-time');
            $jobExtremeSensio->setCompany('Extreme Sensio');
            $jobExtremeSensio->setLogo('job2.jpg');
            $jobExtremeSensio->setUrl('http://www.extreme-sensio.com/');
            $jobExtremeSensio->setPosition('Web Designer');
            $jobExtremeSensio->setLocation('Paris, France');
            $jobExtremeSensio->setDescription('Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in.');
            $jobExtremeSensio->setHowToApply('Send your resume to fabien.potencier [at] sensio.com');
            $jobExtremeSensio->setPublic(true);
            $jobExtremeSensio->setActivated(true);
            $jobExtremeSensio->setEmail('job@example.com');
            $jobExtremeSensio->setCreatedAt(new \DateTime('-30 days'));
            $jobExtremeSensio->setUpdatedAt(new \DateTime());
            $jobExtremeSensio->setExpiresAt(new \DateTime('+30 days'));

            $jobPrivatBank = new Job();
            $jobPrivatBank->setCategory($manager->merge($this->getReference('category-administrator')));
            $jobPrivatBank->setType('full-time');
            $jobPrivatBank->setCompany('Privat Bank');
            $jobPrivatBank->setLogo('job3.jpg');
            $jobPrivatBank->setUrl('http://www.privat-bank.com/');
            $jobPrivatBank->setPosition('Administrator');
            $jobPrivatBank->setLocation('Dnipro, Ukraine');
            $jobPrivatBank->setDescription('Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in.');
            $jobPrivatBank->setHowToApply('Send your resume to I.V. Kolomoisky');
            $jobPrivatBank->setPublic(true);
            $jobPrivatBank->setActivated(true);
            $jobPrivatBank->setEmail('job@example.com');
            $jobPrivatBank->setCreatedAt(new \DateTime('-90 days'));
            $jobPrivatBank->setUpdatedAt(new \DateTime('-20 days'));
            $jobPrivatBank->setExpiresAt(new \DateTime('-10 days'));

            $jobVodafone = new Job();
            $jobVodafone->setCategory($manager->merge($this->getReference('category-manager')));
            $jobVodafone->setType('full-time');
            $jobVodafone->setCompany('Vodafone');
            $jobVodafone->setLogo('d2b2f95416baaae80f95.png');
            $jobVodafone->setUrl('http://www.vodafone.com/');
            $jobVodafone->setPosition('Manager');
            $jobVodafone->setLocation('Ukraine');
            $jobVodafone->setDescription('Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in.');
            $jobVodafone->setHowToApply('Phone us!');
            $jobVodafone->setPublic(true);
            $jobVodafone->setActivated(false);
            $jobVodafone->setEmail('vodafone@example.com');
            $jobVodafone->setCreatedAt(new \DateTime('-90 days'));
            $jobVodafone->setUpdatedAt(new \DateTime('-20 days'));
            $jobVodafone->setExpiresAt(new \DateTime('-10 days'));

            $jobMida = new Job();
            $jobMida->setCategory($manager->merge($this->getReference('category-programming')));
            $jobMida->setType('full-time');
            $jobMida->setCompany('Mida');
            $jobMida->setLogo('dbeecc2401753f094282.png');
            $jobMida->setUrl('http://www.mida.com/');
            $jobMida->setPosition('Programmer');
            $jobMida->setLocation('Ukraine, Zaporizhzhya');
            $jobMida->setDescription('Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in.');
            $jobMida->setHowToApply('Phone us!');
            $jobMida->setPublic(false);
            $jobMida->setActivated(true);
            $jobMida->setEmail('mida@example.com');
            $jobMida->setCreatedAt(new \DateTime('-90 days'));
            $jobMida->setUpdatedAt(new \DateTime('-20 days'));
            $jobMida->setExpiresAt(new \DateTime('-10 days'));

            $manager->persist($jobSensioLabs);
            $manager->persist($jobExtremeSensio);
            $manager->persist($jobPrivatBank);
            $manager->persist($jobVodafone);
            $manager->persist($jobMida);
        }
        $manager->flush();
    }
    public function getDependencies(): array
    {
        return [
            CategoryFixtures::class,
        ];
    }
}
