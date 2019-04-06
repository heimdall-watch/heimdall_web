<?php

namespace App\Controller\Api;

use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class SecurityController extends AbstractController
{
    /**
     * @Route("/", name="api_ping", methods={"GET"})
     */
    public function ping()
    {
        return $this->json(['result' => 'heimdall', 'message' => 'This is a functional Heimdall server.']);
    }

    /**
     * @Route("/test", name="api_test")
     */
    public function index() // TEMP
    {
        return $this->json(['Logged in as ' . $this->getUser()->getUsername() . ' : ' . implode(', ', $this->getUser()->getRoles())]);
    }

    /**
     * @Route("/logout", name="api_logout")
     */
    public function logout() {
        // TODO : Requête dans access_token et refresh_token pour supprimer les enregistrements de l'user
        // TODO : Bouger ça dans un vrai controller
    }
}
