<?php

namespace App\Controller\Api;

use App\Entity\StudentPresence;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Class DefaultController
 * @package App\Controller\Api
 */
class DefaultController extends AbstractController
{
    /**
     * @Rest\Get("/excuses", name="get_excuses")
     * @Rest\View(serializerGroups={"GetExcuse"}, serializerEnableMaxDepthChecks=true)
     *
     * @return String[]
     */
    public function getExcuses()
    {
        return StudentPresence::EXCUSES;
    }
}
