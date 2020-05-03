<?php

namespace App\Controller;

use App\Entity\Student;
use App\Entity\Lesson;
use App\Entity\Presence;
use App\Entity\StudentPresence;
use App\Form\PresenceType;
use App\Form\LessonImportType;
use App\Repository\LessonRepository;
use App\Repository\TeacherRepository;
use App\Repository\StudentRepository;
use App\Repository\StudentPresenceRepository;
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
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ODM\PHPCR\Query\Query;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @Route("/student_user")
 */
class StudentUserController extends AbstractController
{

    /**
     * @Route("/", name="student_user_index", methods={"GET"})
     */
    public function index(Request $request, PaginatorInterface $paginator, StudentRepository $stuRepos, StudentPresenceRepository $repository, TeacherRepository $teaRepo): Response
    {
        $students = $stuRepos->getFindAllQuery()->getResult();
        $stu = $students[0];
        $query = $repository->findAbsencesRetards($stu);
        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            10
        );
        return $this->render('student_user/index.html.twig', [
            'pagination' => $pagination
        ]);
    }

    /**
     * @Route("/new", name="admin_new", methods={"POST"})
     */
    public function new(Request $request, StudentRepository $sr, StudentPresenceRepository $spr,LessonRepository $lr): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $sp = new StudentPresence();
        
        $entityManager->persist($sp);
        $entityManager->flush();
        return new Response();
    }

}