<?php

namespace App\Controller;

use App\Entity\StudentPresence;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

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



}
