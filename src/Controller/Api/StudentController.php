<?php

namespace App\Controller\Api;

use App\Entity\Student;
use App\Entity\StudentPresence;
use App\Form\StudentImageType;
use App\HttpFoundation\File\ApiUploadedFile;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

/**
 * @Route("/student")
 */
class StudentController extends AbstractController
{
    // TODO : AccountController ?
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
        /** @var Student $student */
        $student = $this->getUser();
        if (!$passwordEncoder->isPasswordValid($student, $request->request->get('oldPassword'))) {
            throw new HttpException(403, "Mot de passe actuel incorrect");
        }
        $student->setPlainPassword($request->request->get('newPassword'));
        $this->getDoctrine()->getManager()->flush();

        return true;
    }

    /**
     * @Rest\Post("/photo", name="student_photo_set")
     *
     * @param Request $request
     * @param UploaderHelper $uploaderHelper
     *
     * @return mixed
     */
    public function setPhoto(Request $request, UploaderHelper $uploaderHelper)
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

        return $uploaderHelper->asset($student, 'photoFile');
    }




    /**
     * @Rest\Get("/presences", name="student_get_presences")
     * @Rest\View(serializerGroups={"GetStudentPresences"}, serializerEnableMaxDepthChecks=true)
     *
     * @return StudentPresence[]
     */
    public function getPresences()
    {
        return $this->getDoctrine()->getManager()->getRepository(StudentPresence::class)->findBy(['student' => $this->getUser(), 'present'=>false]);

    }
}