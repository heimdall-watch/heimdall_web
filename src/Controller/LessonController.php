<?php

namespace App\Controller;

use App\Entity\Student;
use App\Entity\Lesson;
use App\Entity\Presence;
use App\Entity\StudentPresence;
use App\Form\PresenceType;
use App\Form\LessonImportType;
use App\Form\LessonType;
use App\Repository\LessonRepository;
use App\Repository\TeacherRepository;
use App\Repository\StudentRepository;
use App\Repository\StudentPresenceRepository;
use App\Security\CheckAccessRights;
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
use DateTime;
use DatePeriod;
use DateInterval;
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
        CheckAccessRights::hasTeacherRole($this->getUser());
        $teacher = $this->getUser();
        //$query = $repository->findNextLesson($teacher);
        $query = $repository->getFindAllQuery();
        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            10
        );
        return $this->render('lesson/index.html.twig', [
            'user' => $teacher,
            'pagination' => $pagination
        ]);
    }

    /**
     * @Route("/{id}", name="lesson_show", methods={"GET"})
     */
    public function show(Lesson $lesson): Response
    {
        CheckAccessRights::hasTeacherRole($this->getUser());
        $teacher = $this->getUser();
        return $this->render('lesson/lesson.html.twig', [
            'user' => $teacher,
            'lesson' => $lesson,
        ]);
    }
    
    /**
     * @Route("/new", name="presence_new", methods={"POST"})
     */
    public function new(Request $request, StudentRepository $sr, StudentPresenceRepository $spr,LessonRepository $lr): Response
    {
        $id_lesson = $request->request->get("id_lesson");
        $id_student = $request->request->get("id_student");
        $present = $request->request->get("present") == "true" ? true : false;
        $late = (int)$request->request->get("late");
        $persist = true;
        $entityManager = $this->getDoctrine()->getManager();
        $sp = new StudentPresence();
        $listStudent = $sr->getFindAllQuery()->getResult();
        $result = ''.$late;
        foreach($listStudent as $s){
            if($s->getId() == $id_student){
                $sp->setStudent($s);
                $listSP = $spr->findAllPresenceStudent($s);
                foreach($listSP as $stdPre){
                    if($stdPre->getlesson()->getId() == $id_lesson){
                        $sp = $entityManager->getRepository(StudentPresence::class)->find($stdPre->getId());
                        $persist = false;
                        break;
                    }
                }
                break;
            }
        }
        
        $listLesson = $lr->getFindAllQuery();
        foreach($listLesson as $les){
            if($les->getId() == $id_lesson){
                $sp->setlesson($les);
                if($late > 0){
                    $dt = new DateInterval('PT'.$late.'M');
                    $dateLate = clone $les->getDateStart();
                    $result = $result.' dateLateBef'.$dateLate->format('Y-m-d h:m');
                    $dateLate->add($dt);
                    $result = $result.' dateLateAf'.$dateLate->format('Y-m-d h:m');
                    $sp->setLate($dateLate);
                }
                break;
            }
        }
        
        $sp->setPresent($present);
        if($persist == true){
            $entityManager->persist($sp);
        }
        $entityManager->flush();
        return new Response($result);
    }

}