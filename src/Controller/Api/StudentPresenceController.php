<?php

namespace App\Controller\Api;

use App\Entity\StudentPresence;
use App\Form\StudentPresenceImageType;
use App\HttpFoundation\File\ApiUploadedFile;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

/**
 * Class StudentPresenceController
 * @package App\Controller\Api
 *
 * @Route("/student/presence")
 */
class StudentPresenceController extends AbstractController
{
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
     * @Rest\Post("/{id}", name="set_excuse")
     *
     * @param Request $request
     * @param UploaderHelper $uploaderHelper
     *
     * @return mixed
    **/
    public function setExcuse(StudentPresence $studentPresence, Request $request, UploaderHelper $uploaderHelper)
    {
        $form = $this->createForm(StudentPresenceImageType::class, $studentPresence);

        $file = new ApiUploadedFile($request->request->get('photoBase64'), $request->request->get('extension'));

        $form->submit(['photoFile' => ['file' => $file], 'excuse' => $request->request->get('excuse')]);

        if (!$form->isSubmitted() || !$form->isValid()) {
            throw new HttpException(Response::HTTP_BAD_REQUEST, "Envoi de la photo impossible");
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($studentPresence);
        $em->flush();

        if ($studentPresence->getPhotoFile() === null) {
            throw new NotFoundHttpException("La photo n'existe pas");
        }

        return $uploaderHelper->asset($studentPresence, 'photoFile');
    }

}