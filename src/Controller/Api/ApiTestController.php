<?php

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ApiTestController extends AbstractController
{
    /**
     * @Route("/api/test", name="api_test")
     */
    public function index()
    {
        return $this->json(['ok']);
    }
}
