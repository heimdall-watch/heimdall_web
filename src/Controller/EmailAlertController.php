<?php

namespace App\Controller;

use App\Entity\EmailAlert;
use App\Form\EmailAlertType;
use App\Repository\EmailAlertRepository;
use App\Security\CheckAccessRights;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/alert")
 */
class EmailAlertController extends AbstractController
{
    /**
     * @Route("/", name="email_alert_index", methods={"GET"})
     * @throws \App\Exception\UserException
     */
    public function index(Request $request, PaginatorInterface $paginator, EmailAlertRepository $repository): Response
    {
        CheckAccessRights::hasAdminOrSuperAdminRole($this->getUser());

        $query = $repository->getFindAllQuery();

        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('email_alert/index.html.twig', [
            'pagination' => $pagination
        ]);
    }

    /**
     * @Route("/new", name="email_alert_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $alert = new EmailAlert();
        $form = $this->createForm(EmailAlertType::class, $alert);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->persist($alert);
            $entityManager->flush();

            return $this->redirectToRoute('email_alert_index');
        }

        return $this->render('email_alert/form.html.twig', [
            'alert' => $alert,
            'form' => $form->createView(),
            'update' => false,
        ]);
    }

    /**
     * @Route("/{id}", name="email_alert_show", methods={"GET"})
     */
    public function show(EmailAlert $alert): Response
    {
        return $this->render('email_alert/show.html.twig', [
            'alert' => $alert,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="email_alert_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, EmailAlert $alert): Response
    {
        $form = $this->createForm(EmailAlertType::class, $alert);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('email_alert_index', [
                'id' => $alert->getId(),
            ]);
        }

        return $this->render('email_alert/form.html.twig', [
            'alert' => $alert,
            'form' => $form->createView(),
            'update' => true,
        ]);
    }

    /**
     * @Route("/{id}", name="email_alert_delete", methods={"DELETE"})
     */
    public function delete(Request $request, EmailAlert $alert): Response
    {
        if ($this->isCsrfTokenValid('delete' . $alert->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($alert);
            $entityManager->flush();
        }

        return $this->redirectToRoute('email_alert_index');
    }
}
