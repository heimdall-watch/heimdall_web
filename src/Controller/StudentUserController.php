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

/**
 * @Route("/student_user")
 */
class StudentUserController extends AbstractController
{

    const IMAGE_FOLDER = "/home/www/heimdall_web/public/image/";

    /**
     * @Route("/", name="student_user_index", methods={"POST", "GET"})
     */
    public function index(Request $request, PaginatorInterface $paginator, StudentPresenceRepository $repository, TeacherRepository $teaRepo): Response
    {
        CheckAccessRights::hasStudentRole($this->getUser());
        $student = $this->getUser();
        $query = $repository->findAbsencesRetards($student);
        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            10
        );
        return $this->render('student_user/index.html.twig', [
            'user' => $student,
            'pagination' => $pagination
        ]);
    }

    /**
     * @Route("/new", name="admin_new", methods={"POST"})
     */
    public function new(Request $request, StudentRepository $sr, StudentPresenceRepository $spr,LessonRepository $lr): Response
    {
        CheckAccessRights::hasStudentRole($this->getUser());
        $entityManager = $this->getDoctrine()->getManager();
        $sp = new StudentPresence();
        
        $entityManager->persist($sp);
        $entityManager->flush();
        return new Response();
    }

    /**
     * @Route("/justify", name="justify_abs", methods={"POST"})
    */
    public function justify(Request $request, StudentRepository $sr, StudentPresenceRepository $spr,LessonRepository $lr): Response
    {
        $file = $request->files->get('justificatif');
        if($file == null || $file->getRealPath() == null || !file_exists($file->getRealPath())){
            echo 'file null';
            return  new Response();
        }
        if(!file_exists("image")){
            mkdir("image");
        }
        copy($file->getRealPath(), "image/".$file->getFilename());
        $path = self::IMAGE_FOLDER.$file->getFilename();
        //$path = $file->getFilename();
        chmod($path, 0644);
        $ids_abs = $request->request->get("ids_abs");
        $ids_abs = preg_split("/[,]+/",$ids_abs);
        foreach($ids_abs as $id){
            if($id==null) continue;
            $entityManager = $this->getDoctrine()->getManager();
            $sp = $entityManager->getRepository(StudentPresence::class)->find($id);
            $sp->setPhotoFile($path);
            $entityManager->flush();
        }
        return $this->redirectToRoute('student_user_index');
    }

}