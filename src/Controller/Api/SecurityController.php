<?php

namespace App\Controller\Api;

use App\Entity\User;
use Doctrine\Common\Collections\Collection;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Gesdinet\JWTRefreshTokenBundle\Entity\RefreshToken;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class SecurityController
 * @package App\Controller\Api
 */
class SecurityController extends AbstractFOSRestController
{
    /**
     * @Rest\Get("/", name="ping")
     */
    public function ping()
    {
        return [
            'server_name' => getenv('HEIMDALL_SERVER_NAME'),
            'result' => 'heimdall',
            'message' => 'This is a functional Heimdall server.',
            'version' => $this->getParameter('heimdall_version'),
            'onesignal_app_id' => getenv('ONESIGNAL_APP_ID'),
        ];
    }

    /**
     * @Rest\Post("/device_subscribe", name="device_subscribe")
     */
    public function deviceSubscribe(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var User $user */
        $user = $this->getUser();

        $user->addDevice($request->request->get('id'));

        $em->merge($user);
        $em->flush();

        return true;
    }

    /**
     * @Rest\Delete("/token/refresh", name="delete_refresh")
     */
    public function deleteRefreshToken() {
        $em = $this->getDoctrine()->getManager();
        /** @var Collection|RefreshToken[] $refreshTokens */
        $refreshTokens = $em->getRepository(RefreshToken::class)->findBy(['username' => $this->getUser()->getUsername()]);
        foreach ($refreshTokens as $refreshToken) {
            $em->remove($refreshToken);
        }

        try {
            $em->flush();
        } catch (\Exception $e) {
            return false;
        }

        return true;
    }
}
