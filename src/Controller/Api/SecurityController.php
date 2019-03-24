<?php

namespace App\Controller\Api;

use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class SecurityController extends AbstractController
{
    /**
     * @Route("/api/test", name="api_test")
     */
    public function index()
    {
        return $this->json(['ok']);
    }

    // TODO
    public function userDetails()
    {

    }

    /**
     * @Route("/api/logout", name="api_logout")
     */
    public function logout() {
        // TODO : Requête dans access_token et refresh_token pour supprimer les enregistrements de l'user
        // TODO : Bouger ça dans un vrai controller
    }
}
