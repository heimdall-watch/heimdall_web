<?php

namespace App\Controller\Api;

use App\Entity\RollCall;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\ConstraintViolationList;

/**
 * Class RollCallController
 * @package App\Controller\Api
 *
 * @Route("/rollcall")
 */
class RollCallController extends AbstractController
{
    /**
     * @Rest\Post("/", name="rollcall_create")
     * @ParamConverter("rollCall", converter="fos_rest.request_body")
     * @IsGranted("ROLE_TEACHER")
     *
     * @param RollCall $rollCall
     * @param ConstraintViolationList $errors
     * @return RollCall|View
     */
    public function create(RollCall $rollCall, ConstraintViolationList $errors)
    {
        if ($errors->count() > 0) {
            return View::create($errors, Response::HTTP_BAD_REQUEST);
        }

        foreach ($rollCall->getStudentPresences() as $studentPresence) {
            $studentPresence->setRollcall($rollCall);
        }
        $em = $this->getDoctrine()->getManager();

        try {
            $em->persist($rollCall);
            $em->flush();
        } catch (\Exception $e) {
            throw new HttpException(Response::HTTP_BAD_REQUEST, null, $e);
        }

        return View::create($rollCall, Response::HTTP_CREATED, ['Location' => null]); // TODO : Location should be the path to get the entity details (REST)
    }
}
