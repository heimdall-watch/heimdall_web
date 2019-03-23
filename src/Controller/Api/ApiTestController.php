<?php

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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

    /**
     * @Route("/api/logout", name="api_logout")
     */
    public function logout() {
        // TODO : Requête dans access_token et refresh_token pour supprimer les enregistrements de l'user
        // TODO : Bouger ça dans un vrai controller
    }
}
