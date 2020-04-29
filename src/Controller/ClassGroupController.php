<?php

namespace App\Controller;

use App\Entity\ClassGroup;
use App\Form\ClassGroupType;
use App\Repository\ClassGroupRepository;
use App\Security\CheckAccessRights;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/class")
 */
class ClassGroupController extends AbstractController
{
    /**
     * @Route("/", name="class_group_index", methods={"GET"})
     * @throws \App\Exception\UserException
     */
    public function index(Request $request, PaginatorInterface $paginator): Response
    {
        CheckAccessRights::hasAdminOrSuperAdminRole($this->getUser());

        $em = $this->getDoctrine()->getManager();
        $query = $em->getRepository(ClassGroup::class)->getFindAllQuery();

        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('class_group/index.html.twig', [
            'pagination' => $pagination
        ]);
    }

    /**
     * @Route("/new", name="class_group_new", methods={"GET","POST"})
     * @throws \App\Exception\UserException
     */
    public function new(Request $request): Response
    {
        CheckAccessRights::hasAdminOrSuperAdminRole($this->getUser());

        $classGroup = new ClassGroup();
        $form = $this->createForm(ClassGroupType::class, $classGroup);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($classGroup);
            $entityManager->flush();

            return $this->redirectToRoute('class_group_index');
        }

        return $this->render('class_group/form.html.twig', [
            'class_group' => $classGroup,
            'form' => $form->createView(),
            'update' => false,
        ]);
    }

    /**
     * @Route("/{id}", name="class_group_show", methods={"GET"})
     */
    public function show(ClassGroup $classGroup): Response
    {
        return $this->render('class_group/show.html.twig', [
            'class_group' => $classGroup,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="class_group_edit", methods={"GET","POST"})
     * @throws \App\Exception\UserException
     */
    public function edit(Request $request, ClassGroup $classGroup): Response
    {
        CheckAccessRights::hasAdminOrSuperAdminRole($this->getUser());

        $form = $this->createForm(ClassGroupType::class, $classGroup);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('class_group_index', [
                'id' => $classGroup->getId(),
            ]);
        }

        return $this->render('class_group/form.html.twig', [
            'class_group' => $classGroup,
            'form' => $form->createView(),
            'update' => true,
        ]);
    }

    /**
     * @Route("/{id}", name="class_group_delete", methods={"DELETE"})
     * @throws \App\Exception\UserException
     */
    public function delete(Request $request, ClassGroup $classGroup): Response
    {
        CheckAccessRights::hasAdminOrSuperAdminRole($this->getUser());

        if ($this->isCsrfTokenValid('delete'.$classGroup->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($classGroup);
            $entityManager->flush();
        }

        return $this->redirectToRoute('class_group_index');
    }
}
