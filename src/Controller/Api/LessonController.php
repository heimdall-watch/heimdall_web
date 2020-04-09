<?php

namespace App\Controller\Api;

use App\Entity\ClassGroup;
use App\Entity\Lesson;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\ConstraintViolationList;

/**
 * Class LessonController
 * @package App\Controller\Api
 *
 * @IsGranted("ROLE_TEACHER")
 * @Route("/lesson")
 */
class LessonController extends AbstractController
{
    /**
     * @Rest\Post("", name="lesson_create")
     * @ParamConverter("lesson", converter="fos_rest.request_body")
     * @Rest\View(serializerGroups={"Getlesson"}, serializerEnableMaxDepthChecks=true)
     *
     * @param Lesson $lesson
     * @param ConstraintViolationList $errors
     * @return Lesson|View
     */
    public function create(Lesson $lesson, ConstraintViolationList $errors)
    {
        if ($errors->count() > 0) {
            return View::create($errors, Response::HTTP_BAD_REQUEST);
        }

        $em = $this->getDoctrine()->getManager();

        foreach ($lesson->getStudentPresences() as $studentPresence) {
            $studentPresence->setlesson($lesson);
        }

        try {
            $em->persist($lesson);
            $em->flush();
        } catch (\Exception $e) {
            throw new HttpException(Response::HTTP_BAD_REQUEST, null, $e);
        }

        return View::create($lesson, Response::HTTP_CREATED, ['Location' => $this->generateUrl('api_lesson_get', ['id' => $lesson->getId()])]); // TODO : Location should be the path to get the entity details (REST)
    }

    /**
     * @Rest\Post("/{id}", name="lesson_update")
     * @ParamConverter("lesson", converter="fos_rest.request_body")
     * @Rest\View(serializerGroups={"Getlesson"}, serializerEnableMaxDepthChecks=true)
     *
     * @param int $id
     * @param Lesson $lesson
     * @param ConstraintViolationList $errors
     * @return Lesson|View
     */
    public function update(int $id, Lesson $lesson, ConstraintViolationList $errors)
    {
        if ($errors->count() > 0) {
            return View::create($errors, Response::HTTP_BAD_REQUEST);
        }

        $existinglesson = $this->getDoctrine()->getRepository(Lesson::class)->find($id);
        if ($existinglesson === null) {
            throw $this->createNotFoundException('The lesson ' . $id . ' does not exists.');
        }

        foreach ($lesson->getStudentPresences() as $studentPresence) {
            $studentPresence->setlesson($lesson);
        }
        $em = $this->getDoctrine()->getManager();

        try {
            $em->merge($lesson);
            $em->flush();
        } catch (\Exception $e) {
            throw new HttpException(Response::HTTP_BAD_REQUEST, null, $e);
        }

        return View::create($lesson, Response::HTTP_CREATED, ['Location' => $this->generateUrl('api_lesson_get', ['id' => $lesson->getId()])]); // TODO : Location should be the path to get the entity details (REST)
    }

    /**
     * @Rest\Get("/{id}", name="lesson_get")
     * @Rest\View(serializerGroups={"Getlesson"}, serializerEnableMaxDepthChecks=true)
     *
     * @param Lesson $lesson
     * @return Lesson
     */
    public function getlesson(Lesson $lesson)
    {
        return $lesson;
    }

    /**
     * @Rest\Get("", name="lesson_get_all")
     * @Rest\View(serializerGroups={"Getlesson"}, serializerEnableMaxDepthChecks=true)
     *
     * @return Lesson[]
     */
    public function getlessons(Request $request)
    {
        return $this->getDoctrine()->getManager()->getRepository(Lesson::class)->findBy(
            ['teacher' => $this->getUser()],
            ['dateStart' => 'DESC'],
            $request->query->get('limit', null));


    }

    /**
     * @Rest\Post("", name="lesson_get_all")
     * @Rest\View(serializerGroups={"Getlesson"}, serializerEnableMaxDepthChecks=true)
     *
     * @return Lesson[]
     */
    public function getLessonsByClassGroup(Request $request, ClassGroup $classGroup)
    {
        return $this->getDoctrine()->getManager()->getRepository(Lesson::class)->findBy(
            ['classGroup' => $classGroup],
            ['dateStart' => 'DESC'],
            $request->query->get('limit', null));

    }

    /**
     * @Rest\Get("/lastweek", name="lesson_get_last_week")
     * @Rest\View(serializerGroups={"Getlesson"}, serializerEnableMaxDepthChecks=true)
     *
     * @return Lesson[]
     */
    public function getlessonsLastWeek(Request $request)
    {return $this->getDoctrine()->getManager()->getRepository(Lesson::class)->findLastWeek($this->getUser());
    }
}
