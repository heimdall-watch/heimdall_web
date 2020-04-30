<?php

namespace App\Controller;

use App\Entity\Student;
use App\Entity\Lesson;
use App\Form\StudentImportType;
use App\Form\LessonImportType;
use App\Form\LessonType;
use App\Repository\LessonRepository;
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
use Doctrine\ODM\PHPCR\Query\Query;

/**
 * @Route("/lesson")
 */
class LessonController extends AbstractController
{

    /**
     * @Route("/", name="lesson_index", methods={"GET"})
     */
    public function index(Request $request, PaginatorInterface $paginator, LessonRepository $repository, TeacherRepository $teaRepo): Response
    {
        /*
        $teachers = $teaRepo->getFindAllQuery();
        //var_dump($teachers);
        $teacher = $teachers[0];
        if($teacher == null){
            $pagination = null;
        } else{
            $query = $repository->findNextLesson($teacher);

            $pagination = $paginator->paginate(
                $query,
                $request->query->getInt('page', 1),
                10
            );
        }
        */
        
        $query = $repository->getFindAllQuery();
        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            10
        );
        return $this->render('lesson/index.html.twig', [
            'pagination' => $pagination
        ]);
    }

    /**
     * @Route("/new", name="lesson_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $lesson = new Lesson();
        $form = $this->createForm(LessonType::class, $lesson);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $lesson->setPlainPassword($lesson->getPassword());

            $entityManager->persist($lesson);
            $entityManager->flush();

            return $this->redirectToRoute('lesson_index');
        }

        return $this->render('lesson/form.html.twig', [
            'lesson' => $lesson,
            'form' => $form->createView(),
            'update' => false,
        ]);
    }

    /**
     * @Route("/{id}", name="lesson_show", methods={"GET"})
     */
    public function show(Lesson $lesson): Response
    {
        return $this->render('lesson/lesson.html.twig', [
            'lesson' => $lesson,
        ]);
    }
    

    /**
     * @Route("/{id}/edit", name="lesson_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Lesson $lesson): Response
    {
        $form = $this->createForm(LessonType::class, $lesson);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('lesson_index', [
                'id' => $lesson->getId(),
            ]);
        }

        return $this->render('lesson/form.html.twig', [
            'lesson' => $lesson,
            'form' => $form->createView(),
            'update' => true,
        ]);
    }

}