<?php

namespace App\Controller;

use App\Entity\StudentPresence;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Vich\UploaderBundle\Handler\DownloadHandler;

/**
 * @Route("/student/presence")
 */
class StudentPresenceController extends AbstractController
{
    /**
     * @Route("/{id}/validate", name="student_presence_validate")
     */
    public function validate(StudentPresence $studentPresence,Request $request)
    {

        if ($this->isCsrfTokenValid('validate' . $studentPresence->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $studentPresence->setExcuseValidated($request->request->get('state') == 'validate');

            $entityManager->flush();
        }

        return $this->redirectToRoute('student_show',['id'=>$studentPresence->getStudent()->getId()]);
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
            return $downloadHandler->downloadObject($studentPresence, 'photoFile');
        } else {
            throw $this->createAccessDeniedException('You do not have access to this photo.');
        }
    }
}
