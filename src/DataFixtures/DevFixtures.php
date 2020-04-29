<?php

namespace App\DataFixtures;

use App\Entity\Admin;
use App\Entity\Auth\Client;
use App\Entity\ClassGroup;
use App\Entity\Lesson;
use App\Entity\Student;
use App\Entity\StudentPresence;
use App\Entity\Teacher;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\ObjectManager;

class DevFixtures extends Fixture
{

    public function load(ObjectManager $manager)
    {
        // Brahim
        $brahim = new Admin();
        $brahim->setUsername("Brahim")
            ->setFirstname("Brahim")
            ->setLastname("LASTNAME")
            ->setEmail("sosthen.gaillard@gmail.com")
            ->setRoles(['ROLE_SUPER_ADMIN'])
            ->setPlainPassword("brahim");
        $manager->persist($brahim);

        // Brahim
        $admin = new Admin();
        $admin->setUsername("admin")
            ->setFirstname("admin")
            ->setLastname("admin")
            ->setEmail("admin.admin@gmail.com")
            ->setRoles(['ROLE_SUPER_ADMIN'])
            ->setPlainPassword("admin");
        $manager->persist($brahim);

        $admins = new ArrayCollection();
        $admins->add($admin);
        $admins->add($brahim);

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
        $miage->setName('M2 Classique')
            ->setFormation('Miage')
            ->setUFR('SEGMI')
            ->setUniversity("Paris Nanterre")
            ->setAdmins($admins);
        $manager->persist($miage);

        // Group M2 APP
        $m2App = new ClassGroup();
        $m2App->setName("M2 Apprentissage")
            ->addStudent($sosthen)
            ->addStudent($julie)
            ->addStudent($flo)
            ->addStudent($loic)
            ->addStudent($samyh)
            ->setFormation('Miage')
            ->setUFR('SEGMI')
            ->setUniversity("Paris Nanterre")
            ->setAdmins($admins);
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

        // lesson 1
        $date = new \DateTime();
        $lessonTest = new Lesson();
        $lessonTest->setClassGroup($m2App)
                    ->setTeacher($jfpp)
                    ->setName('IDD')
                    ->setDateStart(clone $date)
                    ->setDateEnd((clone $date)->modify('+ 2 hours'));
        $manager->persist($lessonTest);

        // lesson 4
        $date = new \DateTime('3 days ago');
        $lessonTest4 = new Lesson();
        $lessonTest4->setClassGroup($m2App)
            ->setTeacher($jfpp)
            ->setName('GDA')
            ->setDateStart(clone $date)
            ->setDateEnd((clone $date)->modify('+ 2 hours'));
        $manager->persist($lessonTest4);

        // lesson 5
        $date = new \DateTime('9 days ago');
        $lessonTest5 = new Lesson();
        $lessonTest5->setClassGroup($m2App)
            ->setTeacher($jfpp)
            ->setName('MSI')
            ->setDateStart(clone $date)
            ->setDateEnd((clone $date)->modify('+ 3 hours'));
        $manager->persist($lessonTest5);

        // lesson 6
        $date = new \DateTime('5 days ago');
        $lessonTest6 = new Lesson();
        $lessonTest6->setClassGroup($m2App)
            ->setTeacher($jfpp)
            ->setName('PROCS')
            ->setDateStart(clone $date)
            ->setDateEnd((clone $date)->modify('+ 4 hours'));
        $manager->persist($lessonTest6);

        // lesson 2
        $date = new \DateTime();
        $lessonTest2 = new Lesson();
        $lessonTest2->setClassGroup($m2App)
            ->setTeacher($jfpp)
            ->setName('EBS')
            ->setDateStart($date)
            ->setDateEnd((clone $date)->modify('+ 6 hours'));
        $manager->persist($lessonTest2);

        // lesson 3
        $date = new \DateTime();
        $lessonTest3 = new Lesson();
        $lessonTest3->setClassGroup($m2App)
            ->setTeacher($jfpp)
            ->setName('Projet')
            ->setDateStart($date)
            ->setDateEnd((clone $date)->modify('+ 2 hours'));
        $manager->persist($lessonTest3);

        // Student Presence 1
        $presenceTest1 = new StudentPresence();
        $presenceTest1->setStudent($sosthen)
                    ->setPresent(false)
                    ->setlesson($lessonTest);
        $manager->persist($presenceTest1);

        // Student Presence 2
        $presenceTest2 = new StudentPresence();
        $presenceTest2->setStudent($sosthen)
            ->setPresent(false)
            ->setlesson($lessonTest2);
        $manager->persist($presenceTest2);

        // Student Presence 3
        $presenceTest3 = new StudentPresence();
        $presenceTest3->setStudent($sosthen)
            ->setPresent(true)
            ->setlesson($lessonTest3);
        $manager->persist($presenceTest3);

        // Student Presence 3
        $presenceTest4 = new StudentPresence();
        $presenceTest4->setStudent($sosthen)
            ->setPresent(true)
            ->setLate(new \DateTime())
            ->setlesson($lessonTest4);
        $manager->persist($presenceTest4);

        $manager->flush();
    }
}
