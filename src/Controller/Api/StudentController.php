<?php

namespace App\Controller\Api;

use App\Entity\Student;
use App\Form\StudentImageType;
use App\HttpFoundation\File\ApiUploadedFile;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Vich\UploaderBundle\Handler\DownloadHandler;

/**
 * @Route("/student")
 * @IsGranted("ROLE_STUDENT")
 */
class StudentController extends UserController
{
    /**
     * @Rest\Post("/update_password", name="student_update_password")
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

        $em = $this->getDoctrine()->getManager();
        $em->persist($student);
        $em->flush();

        if ($student->getPhotoFile() === null) {
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
            return $downloadHandler->downloadObject($student, 'photoFile');
        } else {
            throw $this->createAccessDeniedException('You do not have access to this photo.');
        }
    }
}