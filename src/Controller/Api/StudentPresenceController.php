<?php

namespace App\Controller\Api;

use App\Entity\StudentPresence;
use App\Form\StudentPresenceImageType;
use App\HttpFoundation\File\ApiUploadedFile;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Vich\UploaderBundle\Handler\DownloadHandler;

/**
 * Class StudentPresenceController
 * @package App\Controller\Api
 *
 * @Route("/student/presence")
 * @IsGranted("ROLE_STUDENT")
 */
class StudentPresenceController extends AbstractController
{
    /**
     * @Rest\Get("", name="student_get_presences")
     * @Rest\View(serializerGroups={"GetStudentPresences"}, serializerEnableMaxDepthChecks=true)
     *
     * @return StudentPresence[]
     */
    public function getPresences()
    {
        return $this->getDoctrine()->getManager()->getRepository(StudentPresence::class)->findAbsencesRetards($this->getUser());

    }

    /**
     * @Rest\Get("/presence/{id}", name="get_presence")
     * @Rest\View(serializerGroups={"GetStudentPresences"}, serializerEnableMaxDepthChecks=true)
     *
     * @return StudentPresence
     */
    public function getExcuse(StudentPresence $presence)
    {
        return $presence;
    }

    /**
     * @Rest\Get("/excuses", name="get_excuses")
     * @Rest\View(serializerGroups={"GetExcuse"}, serializerEnableMaxDepthChecks=true)
     *
     * @return String[]
     */
    public function getExcuses()
    {
        return StudentPresence::EXCUSES;
    }

    /**
     * @Rest\Post("/{id}/excuse", name="set_excuse")
     * @Rest\View(serializerGroups={"GetStudentPresences"}, serializerEnableMaxDepthChecks=true)
     *
     * @param Request $request
     *
     * @return mixed
    **/
    public function setExcuse(StudentPresence $studentPresence, Request $request)
    {
        $form = $this->createForm(StudentPresenceImageType::class, $studentPresence);

        $file = new ApiUploadedFile($request->request->get('photoBase64'), $request->request->get('extension'));

        $form->submit(['photoFile' => ['file' => $file], 'excuse' => $request->request->get('excuse')]);

        if (!$form->isSubmitted() || !$form->isValid()) {
            throw new HttpException(Response::HTTP_BAD_REQUEST, "Envoi de la photo impossible");
        }

        $studentPresence->setExcuseValidated(null);

        $this->getDoctrine()->getManager()->flush();

        if ($studentPresence->getPhotoFile() === null) {
            throw new NotFoundHttpException("La photo n'existe pas");
        }

        return View::create($studentPresence, Response::HTTP_CREATED, ['Location' => $this->generateUrl('api_get_presence', ['id' => $studentPresence->getId()])]);
    }

    /**
     * @Route("/{id}/photo", name="get_excuse_proof_photo")
     */
    public function getPhoto(StudentPresence $studentPresence, DownloadHandler $downloadHandler)
    {
        // Only the student owning the file, the admins and the teachers who have the student in their class can access the photo
        if ($this->getUser() == $studentPresence->getStudent() || $this->isGranted('ROLE_ADMIN')) {
            if ($studentPresence->getExcuseProof() === null) {
                throw $this->createNotFoundException('This presence does not have an excuse proof photo.');
            }
            return $downloadHandler->downloadObject($studentPresence, 'photoFile', null, null, false);
        } else {
            throw $this->createAccessDeniedException('You do not have access to this photo.');
        }
    }

}