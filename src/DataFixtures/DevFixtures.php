<?php

namespace App\DataFixtures;

use App\Entity\Admin;
use App\Entity\Auth\Client;
use App\Entity\Student;
use App\Entity\Teacher;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class DevFixtures extends Fixture
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        // Brahim
        $brahim = new Admin();
        $brahim->setUsername("Brahim")
               ->setEmail("sosthen.gaillard@gmail.com")
               ->setPassword($this->encoder->encodePassword($brahim, "brahim"));
        $manager->persist($brahim);

        // Student 1
        $sosthen = new Student();
        $sosthen->setUsername("Sosthen")
               ->setEmail("sosthen.gaillard@gmail.com")
               ->setPassword($this->encoder->encodePassword($sosthen, "sosthen"));
        $manager->persist($sosthen);

        // Teacher 1
        $jfpp = new Teacher();
        $jfpp->setUsername("Jfpp")
                ->setEmail("sosthen.gaillard@gmail.com")
                ->setPassword($this->encoder->encodePassword($jfpp, "jfpp"));
        $manager->persist($jfpp);

        $manager->flush();
    }
}
