<?php

namespace App\Controller;

use App\Entity\Admin;
use App\Entity\ClassGroup;
use App\Entity\Student;
use App\Entity\Teacher;
use App\Form\ImportAllType;
use App\Security\CheckAccessRights;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Port\Csv\CsvReader;
use Port\Spreadsheet\SpreadsheetReader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/import_all")
 */
class ImportAllController extends AbstractController {

    /**
     * @Route("/", name="import_all", methods={"GET", "POST"})
     * @throws \App\Exception\UserException
     */
    public function import(Request $request)
    {
        CheckAccessRights::hasAdminOrSuperAdminRole($this->getUser());

        $em = $this->getDoctrine()->getManager();
        $classes = $this->getDoctrine()->getRepository(ClassGroup::class);
        $teachers = $this->getDoctrine()->getRepository(Teacher::class);
        $students = $this->getDoctrine()->getRepository(Student::class);
        $admins = $this->getDoctrine()->getRepository(Admin::class);
        $data = [];
        $importForm = $this->createForm(ImportAllType::class, $data);
        $importForm->handleRequest($request);

        if ($importForm->isSubmitted()) {
            if ($importForm->isValid()) {
                $data = $importForm->getData();

                /** @var UploadedFile $uploadedFile */
                $uploadedFile = $importForm['file']->getData();
                $mimeType = $uploadedFile->getMimeType();

                $file = new \SplFileObject($uploadedFile->getRealPath());

                if ($mimeType === 'text/plain' || $mimeType === 'text/csv') {
                    $reader = new CsvReader($file);
                } else {
                    $reader = new SpreadsheetReader($file);
                }

                // Skip firstline if headers
                $firstline = true;
                foreach ($reader as $row) {
                    if ($firstline) {
                        $firstline = false;
                        if ($data['firstLineIsHeaders']) {
                            continue;
                        }
                    }

                    $userArray = [
                        'role' => $row[$data['role'] - 1],
                        'username' => $row[$data['username'] - 1],
                        'firstname' => $row[$data['firstname'] - 1],
                        'lastname' => $row[$data['lastname'] - 1],
                        'email' => $row[$data['email'] - 1],
                        'class' => $row[$data['class'] - 1],
                        'university' => $row[$data['university'] - 1],
                        'ufr' => $row[$data['ufr'] - 1],
                        'formation' => $row[$data['formation'] - 1],
                    ];

                    if ($userArray['role'] != "class") {

                        if ($userArray['role'] == "teacher") {
                            $teacher = $teachers->findOneBy([
                                'username' => $userArray['username'],
                                'email' => $userArray['email']
                            ]);
                            if (!empty($teacher)) {
                                continue;
                            }
                            $user = new Teacher();
                            foreach (explode(',', $userArray['class']) as $cl) {
                                if (!empty(trim($cl))) {
                                    $class = $classes->findOneBy([
                                        'name' => trim($cl),
                                        'university' => $userArray['university'],
                                        'UFR' => $userArray['ufr'],
                                        'formation' => $userArray['formation']
                                    ]);
                                    $user->addClassGroup($class);
                                }
                            }
                        } elseif ($userArray['role'] == "student") {
                            $student = $students->findOneBy([
                                'username' => $userArray['username'],
                                'email' => $userArray['email']
                            ]);
                            if (!empty($student)) {
                                continue;
                            }
                            $user = new Student();
                            $class = $classes->findOneBy([
                                'name' => $userArray['class'],
                                'university' => $userArray['university'],
                                'UFR' => $userArray['ufr'],
                                'formation' => $userArray['formation']
                            ]);
                            $user->setClassGroup($class);
                        } elseif ($userArray['role'] == "admin") {
                            $admin = $admins->findOneBy([
                                'username' => $userArray['username'],
                                'email' => $userArray['email']
                            ]);
                            if (!empty($admin)) {
                                continue;
                            }
                            $user = new Admin();
                            foreach (explode(',', $userArray['class']) as $cl) {
                                if (!empty(trim($cl))) {
                                    $class = $classes->findOneBy([
                                        'name' => trim($cl),
                                        'university' => $userArray['university'],
                                        'UFR' => $userArray['ufr'],
                                        'formation' => $userArray['formation']
                                    ]);
                                    $user->addClassGroup($class);
                                }
                            }
                        }

                        $user->setEmail($userArray['email'])
                            ->setFirstname($userArray['firstname'])
                            ->setLastname($userArray['lastname'])
                            ->setUsername($userArray['username']);

                        $em->persist($user);
                        $em->flush();

                    } else {
                        $class = $classes->findOneBy([
                            'name' => $userArray['class'],
                            'university' => $userArray['university'],
                            'UFR' => $userArray['ufr'],
                            'formation' => $userArray['formation']
                        ]);
                        if (empty($class)) {
                            $class = new ClassGroup();
                            $class->setName($userArray['class'])
                                ->setUniversity($userArray['university'])
                                ->setUFR($userArray['ufr'])
                                ->setFormation($userArray['formation']);
                            $em->persist($class);
                            $em->flush();
                        }
                    }
                }

                try {
                    $this->addFlash('success', 'Everything imported!');

                    return $this->redirectToRoute('import_all');
                }

                catch (\Exception $e) {
                    $this->addFlash('danger', 'Import error! Some data might not be in a good format.');
                }
            }
        }

        return $this->render('import_all/index.html.twig', [
            'import_form' => $importForm->createView(),
            'mime_types' => implode(',', ImportAllType::IMPORT_MIME_TYPES),
        ]);
    }

}