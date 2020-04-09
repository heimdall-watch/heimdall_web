<?php

namespace App\Controller;

use App\Entity\Student;
use App\Entity\Teacher;
use App\Form\StudentImportType;
use App\Form\TeacherImportType;
use App\Form\TeacherType;
use App\Repository\TeacherRepository;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Knp\Component\Pager\PaginatorInterface;
use Port\Csv\CsvReader;
use Port\Doctrine\DoctrineWriter;
use Port\Spreadsheet\SpreadsheetReader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/teacher")
 */
class TeacherController extends AbstractController
{
    /**
     * @Route("/import", name="teacher_import", methods={"GET", "POST"})
     */
    public function import(Request $request)
    {
        $data = [];
        $importForm = $this->createForm(TeacherImportType::class, $data);
        $importForm->handleRequest($request);

        if ($importForm->isSubmitted()) {
            if ($importForm->isValid()) {
                try {
                    $this->parseCsv($importForm);
                    $this->addFlash('success', 'Teachers imported!');

                    return $this->redirectToRoute('teacher_index');
                }
                catch (\Exception $e) {
                    $this->addFlash('danger', 'Erreur de l\'import. Vérifiez le CSV.');
                }
            }
        }
        return $this->render('teacher/import.html.twig', [
            'import_form' => $importForm->createView(),
            'mime_types' => implode(',', TeacherImportType::IMPORT_MIME_TYPES),
        ]);
    }

    /**
     * @Route("/", name="teacher_index", methods={"GET"})
     */
    public function index(Request $request, PaginatorInterface $paginator, TeacherRepository $repository): Response
    {
        $query = $repository->getFindAllQuery();

        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('teacher/index.html.twig', [
            'pagination' => $pagination
        ]);
    }

    /**
     * @Route("/new", name="teacher_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $teacher = new Teacher();
        $form = $this->createForm(TeacherType::class, $teacher);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $teacher->setPlainPassword($teacher->getPassword());

            $entityManager->persist($teacher);
            $entityManager->flush();

            return $this->redirectToRoute('teacher_index');
        }

        return $this->render('teacher/form.html.twig', [
            'teacher' => $teacher,
            'form' => $form->createView(),
            'update' => false,
        ]);
    }

    /**
     * @Route("/{id}", name="teacher_show", methods={"GET"})
     */
    public function show(Teacher $teacher): Response
    {
        return $this->render('teacher/show.html.twig', [
            'teacher' => $teacher,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="teacher_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Teacher $teacher): Response
    {
        $form = $this->createForm(TeacherType::class, $teacher);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('teacher_index', [
                'id' => $teacher->getId(),
            ]);
        }

        return $this->render('teacher/form.html.twig', [
            'teacher' => $teacher,
            'form' => $form->createView(),
            'update' => true,
        ]);
    }

    /**
     * @Route("/{id}", name="teacher_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Teacher $teacher): Response
    {
        if ($this->isCsrfTokenValid('delete'.$teacher->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($teacher);
            $entityManager->flush();
        }

        return $this->redirectToRoute('teacher_index');
    }

    /**
     * @param FormInterface $importForm
     * @return bool
     */
    private function parseCsv(FormInterface $importForm)
    {
        $em = $this->getDoctrine()->getManager();

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

        // Skip firstline if headers
        $isfirstline = true;
        foreach ($reader as $row) {
            if ($isfirstline) {
                $isfirstline = false;
                if ($data['firstLineIsHeaders']) {
                    continue;
                }
            }

            $teacherArray = [
                'username' => $row[$data['username'] - 1],
                'firstname' => $row[$data['firstname'] - 1],
                'lastname' => $row[$data['lastname'] - 1],
                'email' => $row[$data['email'] - 1],
                'classGroup' => $classGroup,
            ];

            $teacher = new Teacher();
            $teacher->addClassGroup($classGroup)
                ->setEmail($teacherArray['email'])
                ->setFirstname($teacherArray['firstname'])
                ->setLastname($teacherArray['lastname'])
                ->setUsername($teacherArray['username']);

            $em->persist($teacher);
            $em->flush();
        }
    }
}