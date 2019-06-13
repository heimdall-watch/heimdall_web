<?php

namespace App\Controller\Api;

use App\Entity\RollCall;
use App\Entity\User;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use OneSignal\OneSignal;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\ConstraintViolationList;

/**
 * Class RollCallController
 * @package App\Controller\Api
 *
 * @IsGranted("ROLE_TEACHER")
 * @Route("/rollcall")
 */
class RollCallController extends AbstractController
{
    /**
     * @Rest\Post("", name="rollcall_create")
     * @ParamConverter("rollCall", converter="fos_rest.request_body")
     * @Rest\View(serializerGroups={"GetRollcall"}, serializerEnableMaxDepthChecks=true)
     *
     * @param RollCall $rollCall
     * @param ConstraintViolationList $errors
     * @return RollCall|View
     */
    public function create(RollCall $rollCall, ConstraintViolationList $errors, OneSignal $oneSignal)
    {
        if ($errors->count() > 0) {
            return View::create($errors, Response::HTTP_BAD_REQUEST);
        }

        $em = $this->getDoctrine()->getManager();

        foreach ($rollCall->getStudentPresences() as $studentPresence) {
            if ($studentPresence->getPresent() === false || $studentPresence->getLate() !== null) {
                $student = $em->getRepository(User::class)->find($studentPresence->getStudent()->getId());
                $oneSignal->notifications->add([
                    'contents' => [
                        'en' => 'You are ' . $studentPresence->getPresent() === false ? 'absent' : 'late' . ' to the class of ' . $this->getUser()->getFirstname() . ' ' . $this->getUser()->getLastName(),
                        'fr' => 'Vous êtes ' . $studentPresence->getPresent() === false ? 'absent' : 'en retard' . ' au cours de ' . $this->getUser()->getFirstname() . ' ' . $this->getUser()->getLastName(),
                    ],
                    'include_player_ids' => $student->getDevices()

                ]);
            }
            $studentPresence->setRollcall($rollCall);
        }

        try {
            $em->persist($rollCall);
            $em->flush();
        } catch (\Exception $e) {
            throw new HttpException(Response::HTTP_BAD_REQUEST, null, $e);
        }

        return View::create($rollCall, Response::HTTP_CREATED, ['Location' => $this->generateUrl('api_rollcall_get', ['id' => $rollCall->getId()])]); // TODO : Location should be the path to get the entity details (REST)
    }

    /**
     * @Rest\Post("/{id}", name="rollcall_update")
     * @ParamConverter("rollCall", converter="fos_rest.request_body")
     * @Rest\View(serializerGroups={"GetRollcall"}, serializerEnableMaxDepthChecks=true)
     *
     * @param int $id
     * @param RollCall $rollCall
     * @param ConstraintViolationList $errors
     * @return RollCall|View
     */
    public function update(int $id, RollCall $rollCall, ConstraintViolationList $errors)
    {
        if ($errors->count() > 0) {
            return View::create($errors, Response::HTTP_BAD_REQUEST);
        }

        $existingRollcall = $this->getDoctrine()->getRepository(RollCall::class)->find($id);
        if ($existingRollcall === null) {
            throw $this->createNotFoundException('The rollcall ' . $id . ' does not exists.');
        }

        foreach ($rollCall->getStudentPresences() as $studentPresence) {
            $studentPresence->setRollcall($rollCall);
        }
        $em = $this->getDoctrine()->getManager();

        try {
            $em->merge($rollCall);
            $em->flush();
        } catch (\Exception $e) {
            throw new HttpException(Response::HTTP_BAD_REQUEST, null, $e);
        }

        return View::create($rollCall, Response::HTTP_CREATED, ['Location' => $this->generateUrl('api_rollcall_get', ['id' => $rollCall->getId()])]); // TODO : Location should be the path to get the entity details (REST)
    }

    /**
     * @Rest\Get("/{id}", name="rollcall_get")
     * @Rest\View(serializerGroups={"GetRollcall"}, serializerEnableMaxDepthChecks=true)
     *
     * @param RollCall $rollCall
     * @return RollCall
     */
    public function getRollcall(RollCall $rollCall)
    {
        return $rollCall;
    }

    /**
     * @Rest\Get("", name="rollcall_get_all")
     * @Rest\View(serializerGroups={"GetRollcall"}, serializerEnableMaxDepthChecks=true)
     *
     * @return RollCall[]
     */
    public function getRollcalls(Request $request)
    {
        return $this->getDoctrine()->getManager()->getRepository(RollCall::class)->findBy(
            ['teacher' => $this->getUser()],
            ['dateStart' => 'DESC'],
            $request->query->get('limit', null));


    }

    /**
     * @Rest\Get("/lastweek", name="rollcall_get_last_week")
     * @Rest\View(serializerGroups={"GetRollcall"}, serializerEnableMaxDepthChecks=true)
     *
     * @return RollCall[]
     */
    public function getRollcallsLastWeek(Request $request)
    {
        return $this->getDoctrine()->getManager()->getRepository(RollCall::class)->findLastWeek($this->getUser());
    }


}
