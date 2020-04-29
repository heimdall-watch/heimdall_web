<?php

namespace App\Controller;

use App\Entity\Student;
use App\Form\StudentImportType;
use App\Form\StudentType;
use App\Repository\StudentRepository;
use App\Security\CheckAccessRights;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Knp\Component\Pager\PaginatorInterface;
use Port\Csv\CsvReader;
use Port\Doctrine\DoctrineWriter;
use Port\Spreadsheet\SpreadsheetReader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Vich\UploaderBundle\Handler\DownloadHandler;


/**
 * @Route("/student")
 */
class StudentController extends AbstractController
{
    /**
     * @Route("/import", name="student_import", methods={"GET", "POST"})
     * @throws \App\Exception\UserException
     */
    public function import(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        CheckAccessRights::hasAdminOrSuperAdminRole($this->getUser());

        $em = $this->getDoctrine()->getManager();
        $data = [];
        $importForm = $this->createForm(StudentImportType::class, $data);
        $importForm->handleRequest($request);

        if ($importForm->isSubmitted()) {
            if ($importForm->isValid()) {
                $data = $importForm->getData();
                $classGroup = $data['classGroup'];

                /** @var UploadedFile $uploadedFile */
                $uploadedFile = $importForm['file']->getData();
                $mimeType = $uploadedFile->getMimeType();

                $file = new \SplFileObject($uploadedFile->getRealPath());

                if ($mimeType === 'text/plain' || $mimeType === 'text/csv') {
                    $reader = new CsvReader($file);
                } else {
                    $reader = new SpreadsheetReader($file);
                }
                $writer = new DoctrineWriter($em, Student::class);
                $writer->disableTruncate();

                $writer->prepare();

                // Skip firstline if headers
                $firstline = true;
                foreach ($reader as $row) {
                    if ($firstline) {
                        $firstline = false;
                        if ($data['firstLineIsHeaders']) {
                            continue;
                        }
                    }

                    $associatedRow = [
                        'username' => $row[$data['username']-1],
                        'firstname' => $row[$data['firstname']-1],
                        'lastname' => $row[$data['lastname']-1],
                        'email' => $row[$data['email']-1],
                        'classGroup' => $classGroup,
                    ];

                    $student = new Student();
                    $student->setClassGroup($classGroup)
                        ->setEmail($associatedRow['email'])
                        ->setFirstname($associatedRow['firstname'])
                        ->setLastname($associatedRow['lastname'])
                        ->setUsername($associatedRow['username']);

                    $em->persist($student);
                    $em->flush();
                }

                try {
                    $writer->finish();
                    $this->addFlash('success', 'Students imported!');

                    return $this->redirectToRoute('student_index');
                }
                catch (UniqueConstraintViolationException $e) {
                    $this->addFlash('danger', 'Some students already exists, please remove the duplicates before import.');
                }
                catch (\Exception $e) {
                    $this->addFlash('danger', 'Import error! Some data might not be in a good format.');
                }
            }
        }

        return $this->render('student/import.html.twig', [
            'import_form' => $importForm->createView(),
            'mime_types' => implode(',', StudentImportType::IMPORT_MIME_TYPES),
        ]);
    }

    /**
     * @Route("/", name="student_index", methods={"GET"})
     * @throws \App\Exception\UserException
     */
    public function index(Request $request, PaginatorInterface $paginator, StudentRepository $repository): Response
    {
        CheckAccessRights::hasAdminOrSuperAdminRole($this->getUser());

        $query = $repository->getFindAllQuery();

        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('student/index.html.twig', [
            'pagination' => $pagination
        ]);
    }

    /**
     * @Route("/new", name="student_new", methods={"GET","POST"})
     * @throws \App\Exception\UserException
     */
    public function new(Request $request): Response
    {
        CheckAccessRights::hasAdminOrSuperAdminRole($this->getUser());

        $student = new Student();
        $form = $this->createForm(StudentType::class, $student, ['userId' => $student->getId()]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->persist($student);
            $entityManager->flush();

            return $this->redirectToRoute('student_index');
        }

        return $this->render('student/form.html.twig', [
            'student' => $student,
            'form' => $form->createView(),
            'update' => false,
        ]);
    }

    /**
     * @Route("/{id}", name="student_show", methods={"GET"})
     * @throws \App\Exception\UserException
     */
    public function show(Student $student): Response
    {
        CheckAccessRights::hasAdminOrSuperAdminRole($this->getUser());

        return $this->render('student/show.html.twig', [
            'student' => $student,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="student_edit", methods={"GET","POST"})
     * @throws \App\Exception\UserException
     */
    public function edit(Request $request, Student $student): Response
    {
        CheckAccessRights::hasAdminOrSuperAdminRole($this->getUser());

        $form = $this->createForm(StudentType::class, $student, ['userId' => $student->getId()]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('student_index', [
                'id' => $student->getId(),
            ]);
        }

        return $this->render('student/form.html.twig', [
            'student' => $student,
            'form' => $form->createView(),
            'update' => true,
        ]);
    }

    /**
     * @Route("/{id}", name="student_delete", methods={"DELETE"})
     * @throws \App\Exception\UserException
     */
    public function delete(Request $request, Student $student): Response
    {
        CheckAccessRights::hasAdminOrSuperAdminRole($this->getUser());

        if ($this->isCsrfTokenValid('delete' . $student->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($student);
            $entityManager->flush();
        }

        return $this->redirectToRoute('student_index');
    }

    /**
     * @Route("/{id}/photo", name="student_get_photo")
     */
    public function getPhoto(Student $student, DownloadHandler $downloadHandler)
    {
        // Only the student owning the file, the admins and the teachers who have the student in their class can access the photo
        if ($this->getUser() == $student || $this->isGranted('ROLE_ADMIN') || ($this->isGranted('ROLE_TEACHER') && $this->getUser()->teachToStudent($student))) {
            if ($student->getPhoto() === null) {
                throw $this->createNotFoundException('This student does not have a photo');
            }
            return $downloadHandler->downloadObject($student, 'photoFile', null, null, false);
        } else {
            throw $this->createAccessDeniedException('You do not have access to this photo.');
        }
    }
}
