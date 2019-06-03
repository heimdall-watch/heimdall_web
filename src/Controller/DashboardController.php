<?php

namespace App\Controller;

use App\Repository\StudentPresenceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    /**
     * @Route("/", name="dashboard")
     */
    public function index(StudentPresenceRepository $repository, Request $request)
    {
        return $this->render('index.html.twig', [
            'absences' => $repository->findByDate(new \DateTime($request->query->has('date') ? $request->query->get('date') : 'midnight today')),
        ]);

    }
}
