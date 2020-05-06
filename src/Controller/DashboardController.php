<?php

namespace App\Controller;

use App\Entity\StudentPresence;
use App\Repository\StudentPresenceRepository;
use App\Security\CheckAccessRights;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    /**
     * @Route("/", name="dashboard")
     * @throws \App\Exception\UserException
     */
    public function index()
    {
        CheckAccessRights::hasAdminOrSuperAdminRole($this->getUser());

        $em = $this->getDoctrine()->getManager();

        return $this->render('index.html.twig', [
            'absences' => $em->getRepository(StudentPresence::class)->findPending(),
        ]);
    }
}
