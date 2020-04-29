<?php

namespace App\Controller\Api;

use App\Entity\ClassGroup;
use App\Entity\Lesson;
use App\Entity\Student;
use App\Entity\Teacher;
use App\Entity\User;
use FOS\RestBundle\Controller\Annotations as Rest;
use http\Env\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/teacher")
 * @IsGranted("ROLE_TEACHER")
 */
class TeacherController extends UserController
{
    /**
     * @Rest\Post("/update_password", name="teacher_update_password")
     *
     * @param Request                      $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     *
     * @return bool
     */
    public function updatePassword(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        return parent::updatePassword($request, $passwordEncoder);
    }

    /**
     * @Rest\Post("/reset_password", name="teacher_reset_password")
     *
     * @param User $user
     *
     */
    public function resetPassword(User $user)
    {
        return parent::resetPassword($user);
    }

    /**
     * @Rest\Get("/{id}/lessons/get", name="lesson_get_all")
     * @Rest\View(serializerGroups={"Getlesson"}, serializerEnableMaxDepthChecks=true)
     */
    public function getlessons(Request $request, int $id)
    {
        $em = $this->getDoctrine()->getManager();

        /** @var Teacher $teacher */
        $teacher = $em->getRepository(Teacher::class)->find($id);

        if ($teacher === null) {
            return new JsonResponse('Teacher not found', 404);
        }

        $response = [];
        $response['teacher']['id'] = $teacher->getId();
        $response['teacher']['username '] = $teacher->getUsername();
        $response['teacher']['firstname'] = $teacher->getFirstname();
        $response['teacher']['lastname'] = $teacher->getLastname();
        $response['teacher']['role'] = $teacher->getRoles();

        $teacherLessons = $teacher->getLessons();

        if (count($teacherLessons) === 0) {
            return new JsonResponse('Teacher does not have any lessons yet', 500 );
        }

        foreach ($teacherLessons as $teacherLesson) {
            $lessonName = $teacherLesson->getName();
            $classGroup = $teacherLesson->getClassGroup();
            $className = $teacherLesson->getClassGroup()->getName();

            $response['teacher'][$lessonName]['id'] = $teacherLesson->getId();
            $response['teacher'][$lessonName]['name'] = $teacherLesson->getName();
            $response['teacher'][$lessonName][$className] = $teacherLesson->getLesson()->getName();
            $response['teacher'][$lessonName][$className]['schedule']['beginning'] = $teacherLesson->getDateStart();
            $response['teacher'][$lessonName][$className]['schedule']['ending'] = $teacherLesson->getDateEnd();

            $students = $classGroup->getStudents();
            if (count($students) === 0) {
                return new JsonResponse('The lesson does not have any students', 500);
            }

            /** @var Student $student */
            foreach ($students as $student) {

                $studentUsername = $student->getUsername();
                $isStudentPresent = $this->isStudentPresentForLesson($student, $teacherLesson);
                $studentLateness = $this->getStudentLatenessForLesson($student, $teacherLesson);

                $response['teacher'][$lessonName][$className][$studentUsername]['id'] = $student->getId();
                $response['teacher'][$lessonName][$className][$studentUsername]['firstname'] = $student->getFirstname();
                $response['teacher'][$lessonName][$className][$studentUsername]['lastname'] = $student->getLastname();
                //TODO: GET THE STUDENT PHOTO
                $response['teacher'][$lessonName][$className][$studentUsername]['photo'] = '';
                $response['teacher'][$lessonName][$className][$studentUsername]['attendance']['present'] = $isStudentPresent;

                if ($isStudentPresent) {
                    $response['teacher'][$lessonName][$className][$studentUsername]['attendance']['late'] = $studentLateness;
                }

            }
        }

        return new JsonResponse($response, 500);
    }

    private function isStudentPresentForLesson(Student $student, Lesson $lesson)
    {
        foreach ($student->getPresences() as $presence) {
            if ($presence->getlesson() === $lesson && $presence->getPresent()) {
                return true;
            }
        }

        return false;
    }

    private function getStudentLatenessForLesson(Student $student, Lesson $lesson)
    {
        foreach ($student->getPresences() as $presence) {
            $studentLateness = $presence->getLate();
            if ($presence->getlesson() === $lesson && $studentLateness !== null) {
                return $studentLateness;
            }
        }

        return null;
    }
}