<?php

namespace App\Controller;

use App\Repository\StudentPresenceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    /**
     * @Route("/", name="dashboard")
     * @param StudentPresenceRepository $repository
     * @return Response
     */
    public function index(StudentPresenceRepository $repository)
    {
        return $this->render('index.html.twig', [
            'absences' => $repository->findPending(),
        ]);

    }
}
