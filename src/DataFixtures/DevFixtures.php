<?php

namespace App\DataFixtures;

use App\Entity\Admin;
use App\Entity\Auth\Client;
use App\Entity\ClassGroup;
use App\Entity\RollCall;
use App\Entity\Student;
use App\Entity\StudentPresence;
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

        // Group M2 APP
        $group = new ClassGroup();
        $group->setName("M2 APP");
        $manager->persist($group);

        // Rollcall 1
        $date = new \DateTime();
        $rollcallTest = new RollCall();
        $rollcallTest->setClassGroup($group)
                    ->setTeacher($jfpp)
                    ->setDate($date)
                    ->setDuration(1);
        $manager->persist($rollcallTest);

        // Student Presence 1
        $presenceTest = new StudentPresence();
        $presenceTest->setStudent($sosthen)
                    ->setPresent(true)
                    ->setRollcall($rollcallTest);
        $manager->persist($presenceTest);

        $manager->flush();
    }
}
