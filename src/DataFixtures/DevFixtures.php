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
    {                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                           $this      and
        // Brahim
        $brahim = new Admin();
        $brahim->setUsername("Brahim")
               ->setFirstname("Brahim")
               ->setLastname("Lastname")
               ->setEmail("sosthen.gaillard@gmail.com")
               ->setPlainPassword("brahim");
        $manager->persist($brahim);

        // Student 1
        $sosthen = new Student();
        $sosthen->setUsername("Sosthen")
                ->setFirstname("Sosthen")
                ->setLastname("Lastname")
                ->setEmail("sosthen.gaillard@gmail.com")
                ->setPlainPassword("sosthen");
        $manager->persist($sosthen);

        $miage = new ClassGroup();
        $miage->setName('MIAGE')->addStudent($sosthen);
        $manager->persist($miage);

        // Teacher 1
        $jfpp = new Teacher();
        $jfpp->setUsername("Jfpp")
             ->setFirstname("Jfpp")
             ->setLastname("Lastname")
             ->setEmail("sosthen.gaillard@gmail.com")
             ->setPlainPassword("jfpp");
        $manager->persist($jfpp);

        // Group M2 APP
        $group = new ClassGroup();
        $group->setName("M2 APP");
        $manager->persist($group);

        // Rollcall 1
        $date = new \DateTime;
        $rollcallTest = new RollCall();
        $rollcallTest->setClassGroup($group)
                    ->setTeacher($jfpp)
                    ->setDateStart((clone $date)->modify('2 days ago'))
                    ->setDateEnd((clone $date)->modify('+ 2 hours'));
        $manager->persist($rollcallTest);

        // Rollcall 4
        $date = new \DateTime;
        $rollcallTest4 = new RollCall();
        $rollcallTest4->setClassGroup($group)
            ->setTeacher($jfpp)
            ->setDateStart((clone $date)->modify('3 days ago'))
            ->setDateEnd((clone $date)->modify('+ 2 hours'));
        $manager->persist($rollcallTest4);

        // Rollcall 5
        $date = new \DateTime;
        $rollcallTest5 = new RollCall();
        $rollcallTest5->setClassGroup($group)
            ->setTeacher($jfpp)
            ->setDateStart((clone $date)->modify('9 days ago'))
            ->setDateEnd((clone $date)->modify('+ 3 hours'));
        $manager->persist($rollcallTest5);

        // Rollcall 6
        $date = new \DateTime;
        $rollcallTest6 = new RollCall();
        $rollcallTest6->setClassGroup($group)
            ->setTeacher($jfpp)
            ->setDateStart((clone $date)->modify('5 days ago'))
            ->setDateEnd((clone $date)->modify('+ 4 hours'));
        $manager->persist($rollcallTest6);

        // Rollcall 2
        $date = new \DateTime();
        $rollcallTest2 = new RollCall();
        $rollcallTest2->setClassGroup($group)
            ->setTeacher($jfpp)
            ->setDateStart($date)
            ->setDateEnd((clone $date)->modify('+ 6 hours'));
        $manager->persist($rollcallTest2);

        // Rollcall 3
        $date = new \DateTime();
        $rollcallTest3 = new RollCall();
        $rollcallTest3->setClassGroup($group)
            ->setTeacher($jfpp)
            ->setDateStart($date)
            ->setDateEnd((clone $date)->modify('+ 2 hours'));
        $manager->persist($rollcallTest3);

        // Student Presence 1
        $presenceTest1 = new StudentPresence();
        $presenceTest1->setStudent($sosthen)
                    ->setPresent(false)
                    ->setRollcall($rollcallTest);
        $manager->persist($presenceTest1);

        // Student Presence 2
        $presenceTest2 = new StudentPresence();
        $presenceTest2->setStudent($sosthen)
            ->setPresent(false)
            ->setRollcall($rollcallTest2);
        $manager->persist($presenceTest2);

        // Student Presence 3
        $presenceTest3 = new StudentPresence();
        $presenceTest3->setStudent($sosthen)
            ->setPresent(true)
            ->setRollcall($rollcallTest3);
        $manager->persist($presenceTest3);

        $manager->flush();
    }
}
