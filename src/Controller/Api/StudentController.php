<?php

namespace App\Controller\Api;

use App\Entity\Lesson;
use App\Entity\Student;
use App\Entity\StudentPresence;
use App\Entity\User;
use App\Form\StudentImageType;
use App\HttpFoundation\File\ApiUploadedFile;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Vich\UploaderBundle\Handler\DownloadHandler;

/**
 * @Route("/student")
 */
class StudentController extends UserController
{
    /**
     * @Rest\Post("/update_password", name="student_update_password")
     * @IsGranted("ROLE_STUDENT")
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
     * @Rest\Post("/reset_password", name="student_reset_password")
     * @IsGranted("ROLE_STUDENT")
     *
     * @param User $user
     *
     */
    public function resetPassword(User $user)
    {
        return parent::resetPassword($user);
    }

    /**
     * @Rest\Post("/photo", name="student_photo_set")
     * @IsGranted("ROLE_STUDENT")
     *
     * @param Request $request
     *
     * @return mixed
     */
    public function setPhoto(Request $request)
    {
        /** @var Student $student */
        $student = $this->getUser();

        $form = $this->createForm(StudentImageType::class, $student);

        $file = new ApiUploadedFile($request->request->get('photoBase64'), $request->request->get('extension'));

        $form->submit(['photoFile' => ['file' => $file]]);

        if (!$form->isSubmitted() || !$form->isValid()) {
            throw new HttpException(Response::HTTP_BAD_REQUEST, "Envoi de la photo impossible");
        }

        $this->getDoctrine()->getManager()->flush();

        if ($student->getPhoto() === null) {
            throw new NotFoundHttpException("La photo n'existe pas");
        }

        return $this->generateUrl('api_student_get_photo', ['id' => $student->getId()], RouterInterface::ABSOLUTE_URL);
    }

    /**
     * @Route("/{id}/photo", name="student_get_photo")
     */
    public function getPhoto(Student $student, DownloadHandler $downloadHandler) // TODO : Refacto with classic StudentController
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

    /**
     * @Rest\Get("/{id}/attendances", name="get_attendances")
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function getStudentName(Request $request, int $id) {
        $em = $this->getDoctrine()->getManager();

        /** @var Student $student */
        $student = $em->getRepository(Student::class)->find($id);

        if ($student === null) {
            return new JsonResponse('Student not found', 404);
        }

        $response = [];
        $response['student']['id'] = $student->getId();
        $response['student']['username'] = $student->getUsername();
        $response['student']['firstname'] = $student->getFirstname();
        $response['student']['lastname'] = $student->getLastname();
        $response['student']['role'] = $student->getRoles();

        /** @var StudentPresence $presences */
        $presences = $student->getPresences();

        /** @var StudentPresence $presence */
        foreach ($presences as $presence) {
            if (!$presence->getPresent() || ($presence->getPresent() && !is_null($presence->getLate()))) {
                /** @var Lesson $lesson */
                $lesson = $presence->getLesson();
                $lessonName = $lesson->getName();
                $response['student'][$lessonName]['id'] = $lesson->getId();
                $response['student'][$lessonName]['schedule']['beginning'] = $lesson->getDateStart();
                $response['student'][$lessonName]['schedule']['ending'] = $lesson->getDateEnd();
                $response['student'][$lessonName]['attendance']['presence'] = $presence->getPresent();
                $response['student'][$lessonName]['attendance']['excuse']['description'] = $presence->getExcuseDescription();
                $response['student'][$lessonName]['attendance']['excuse']['proof'] = $presence->getExcuseProof();
                $response['student'][$lessonName]['attendance']['excuse']['isValidated'] = $presence->getExcuseValidated();
                $response['student'][$lessonName]['attendance']['delay'] = $presence->getLate();
            }
        }

        return new JsonResponse($response, 500);
    }

}