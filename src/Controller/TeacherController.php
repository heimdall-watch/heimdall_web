<?php

namespace App\Controller;

use App\Entity\Teacher;
use App\Form\TeacherType;
use App\Repository\TeacherRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/teacher")
 */
class TeacherController extends AbstractController
{
    /**
     * @Route("/", name="teacher_index", methods={"GET"})
     */
    public function index(Request $request, PaginatorInterface $paginator, TeacherRepository $repository): Response
    {
        $query = $repository->getFindAllQuery();

        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('teacher/index.html.twig', [
            'pagination' => $pagination
        ]);
    }

    /**
     * @Route("/new", name="teacher_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $teacher = new Teacher();
        $form = $this->createForm(TeacherType::class, $teacher);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $teacher->setPlainPassword($teacher->getPassword());


            $entityManager->persist($teacher);
            $entityManager->flush();

            return $this->redirectToRoute('teacher_index');
        }

        return $this->render('teacher/form.html.twig', [
            'teacher' => $teacher,
            'form' => $form->createView(),
            'update' => false,
        ]);
    }

    /**
     * @Route("/{id}", name="teacher_show", methods={"GET"})
     */
    public function show(Teacher $teacher): Response
    {
        return $this->render('teacher/show.html.twig', [
            'teacher' => $teacher,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="teacher_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Teacher $teacher): Response
    {
        $form = $this->createForm(TeacherType::class, $teacher);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('teacher_index', [
                'id' => $teacher->getId(),
            ]);
        }

        return $this->render('teacher/form.html.twig', [
            'teacher' => $teacher,
            'form' => $form->createView(),
            'update' => true,
        ]);
    }

    /**
     * @Route("/{id}", name="teacher_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Teacher $teacher): Response
    {
        if ($this->isCsrfTokenValid('delete'.$teacher->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($teacher);
            $entityManager->flush();
        }

        return $this->redirectToRoute('teacher_index');
    }
}
