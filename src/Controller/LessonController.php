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
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

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
     * @Route("/{id}", name="lesson_show", methods={"GET"})
     */
    public function show(Lesson $lesson): Response
    {
        return $this->render('lesson/lesson.html.twig', [
            'lesson' => $lesson,
        ]);
    }
    
    /**
     * @Route("/new", name="admin_new", methods={"GET","POST"})
     */
    public function new(Request $request, StudentRepository $sr, StudentPresenceRepository $spr,LessonRepository $lr): Response
    {
        $presence = new Presence();
        $form = $this->createForm(PresenceType::class, $presence);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $sp = new StudentPresence();
            $listSP = $spr->getFindAllQuery()->getResult();
            foreach($listSP as $stdPre){
                if($stdPre->getStudent()->getId() == $presence->getIdStudent() 
                    && $stdPre->getlesson()->getId() == $presence->getIdLesson()){
                    $sp = $stdPre;
                }
            }
            $listLesson = $lr->getFindAllQuery()->getResult();
            foreach($listLesson as $les){
                if($les->getId() == $presence->getIdLesson()){
                    $sp->setlesson($les);
                    if($presence->getLate() > 0){
                        $dt = new DateInterval("PT".$presence->getLate()."M");
                        $dateLate = $les->setDateStart()->add($dt);
                        $sp->setLate($dateLate);
                    }
                    break;
                }
            }
            $listStudent = $sr->getFindAllQuery()->getResult();
            foreach($listStudent as $s){
                if($s->getId() == $presence->getIdStudent()){
                    $sp->setStudent($s);
                    break;
                }
            }
            if($presence->getPresent() == "true"){
                $sp->setPresent(true);
            } else {
                $sp->setPresent(false);
            }
            $entityManager->persist($sp);
            $entityManager->flush();

        }

        //return $this->redirectToRoute('lesson_index');
    }

}