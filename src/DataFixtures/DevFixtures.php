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
               ->setLastname("Boughezala")
               ->setEmail("sosthen.gaillard@gmail.com")
               ->setPlainPassword("brahim");
        $manager->persist($brahim);

        // Student 1
        $sosthen = new Student();
        $sosthen->setUsername("Sosthen")
                ->setFirstname("Sosthen")
                ->setLastname("Gaillard")
                ->setEmail("sosthen@heimdall.watch")
                ->setPlainPassword("sosthen");
        $manager->persist($sosthen);
        $julie = new Student();
        $julie->setUsername("Julie")
            ->setFirstname("Julie")
            ->setLastname("Ausseil")
            ->setEmail("julie@heimdall.watch")
            ->setPlainPassword("julie");
        $manager->persist($julie);
        $flo = new Student();
        $flo->setUsername("Florence")
            ->setFirstname("Florence")
            ->setLastname("Allard")
            ->setEmail("florence@heimdall.watch")
            ->setPlainPassword("florence");
        $manager->persist($flo);
        $loic = new Student();
        $loic->setUsername("Loic")
            ->setFirstname("Loïc")
            ->setLastname("Bonnet")
            ->setEmail("loic@heimdall.watch")
            ->setPlainPassword("loic");
        $manager->persist($loic);
        $samyh = new Student();
        $samyh->setUsername("Samyh")
            ->setFirstname("Samyh")
            ->setLastname("Bouleoud")
            ->setEmail("samyh@heimdall.watch")
            ->setPlainPassword("samyh");
        $manager->persist($samyh);

        // Group MIAGE
        $miage = new ClassGroup();
        $miage->setName('M2 Classique');
        $manager->persist($miage);

        // Group M2 APP
        $m2App = new ClassGroup();
        $m2App->setName("M2 Apprentissage")
            ->addStudent($sosthen)
            ->addStudent($julie)
            ->addStudent($flo)
            ->addStudent($loic)
            ->addStudent($samyh);
        $manager->persist($m2App);

        for ($i = 0; $i < 10; $i++) {
            $student = new Student();
            $student->setUsername("Student " . $i)
                ->setFirstname("Student " . $i)
                ->setLastname("Default " . $i)
                ->setEmail("student@student.student");
            $manager->persist($student);
            $m2App->addStudent($student);
        }

        // Teacher 1
        $jfpp = new Teacher();
        $jfpp->addClassGroup($m2App)
            ->addClassGroup($miage)
            ->setUsername("Jfpp")
            ->setFirstname("Jfpp")
            ->setLastname("Lastname")
            ->setEmail("sosthen.gaillard@gmail.com")
            ->setPlainPassword("jfpp");
        $manager->persist($jfpp);

        // Rollcall 1
        $date = new \DateTime;
        $rollcallTest = new RollCall();
        $rollcallTest->setClassGroup($m2App)
                    ->setTeacher($jfpp)
                    ->setDateStart((clone $date)->modify('2 days ago'))
                    ->setDateEnd((clone $date)->modify('+ 2 hours'));
        $manager->persist($rollcallTest);

        // Rollcall 4
        $date = new \DateTime;
        $rollcallTest4 = new RollCall();
        $rollcallTest4->setClassGroup($m2App)
            ->setTeacher($jfpp)
            ->setDateStart((clone $date)->modify('3 days ago'))
            ->setDateEnd((clone $date)->modify('+ 2 hours'));
        $manager->persist($rollcallTest4);

        // Rollcall 5
        $date = new \DateTime;
        $rollcallTest5 = new RollCall();
        $rollcallTest5->setClassGroup($m2App)
            ->setTeacher($jfpp)
            ->setDateStart((clone $date)->modify('9 days ago'))
            ->setDateEnd((clone $date)->modify('+ 3 hours'));
        $manager->persist($rollcallTest5);

        // Rollcall 6
        $date = new \DateTime;
        $rollcallTest6 = new RollCall();
        $rollcallTest6->setClassGroup($m2App)
            ->setTeacher($jfpp)
            ->setDateStart((clone $date)->modify('5 days ago'))
            ->setDateEnd((clone $date)->modify('+ 4 hours'));
        $manager->persist($rollcallTest6);

        // Rollcall 2
        $date = new \DateTime();
        $rollcallTest2 = new RollCall();
        $rollcallTest2->setClassGroup($m2App)
            ->setTeacher($jfpp)
            ->setDateStart($date)
            ->setDateEnd((clone $date)->modify('+ 6 hours'));
        $manager->persist($rollcallTest2);

        // Rollcall 3
        $date = new \DateTime();
        $rollcallTest3 = new RollCall();
        $rollcallTest3->setClassGroup($m2App)
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
