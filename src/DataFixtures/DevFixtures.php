<?php

namespace App\DataFixtures;

use App\Entity\Admin;
use App\Entity\Auth\Client;
use App\Entity\ClassGroup;
use App\Entity\RollCall;
use App\Entity\Student;
use App\Entity\StudentPresence;
use App\Entity\Teacher;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class DevFixtures extends Fixture
{

    public function load(ObjectManager $manager)
    {
        // Brahim
        $brahim = new Admin();
        $brahim->setUsername("Brahim")
               ->setEmail("sosthen.gaillard@gmail.com")
               ->setPlainPassword("brahim");
        $manager->persist($brahim);

        // Student 1
        $sosthen = new Student();
        $sosthen->setUsername("Sosthen")
               ->setEmail("sosthen.gaillard@gmail.com")
               ->setPlainPassword("sosthen");
        $manager->persist($sosthen);

        $miage = new ClassGroup();
        $miage->setName('MIAGE')->addStudent($sosthen);
        $manager->persist($miage);

        // Teacher 1
        $jfpp = new Teacher();
        $jfpp->setUsername("Jfpp")
                ->setEmail("sosthen.gaillard@gmail.com")
                ->setPlainPassword("jfpp");
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
                    ->setDateStart($date)
                    ->setDateEnd((clone $date)->modify('+ 2 hours'));
        $manager->persist($rollcallTest);

        // Student Presence 1
        $presenceTest = new StudentPresence();
        $presenceTest->setStudent($sosthen)
                    ->setPresent(false)
                    ->setRollcall($rollcallTest);
        $manager->persist($presenceTest);

        $manager->flush();
    }
}
