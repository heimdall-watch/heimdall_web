<?php

namespace App\EventListener;

use App\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;

class ApiAuthenticationSuccessListener
{
    private $tokenExpires;
    private $refreshTokenExpires;

    public function __construct($tokenExpires, $refreshTokenExpires)
    {
        $this->tokenExpires = $tokenExpires;
        $this->refreshTokenExpires = $refreshTokenExpires;
    }

    /**
     * @param AuthenticationSuccessEvent $event
     */
    public function onAuthenticationSuccess(AuthenticationSuccessEvent $event)
    {
        $data = $event->getData();
        $user = $event->getUser();

        if (!$user instanceof User) {
            return;
        }

        $data['token_expires'] = $this->tokenExpires;
        if (\array_key_exists('refresh_token', $data)) {
            $data['refresh_token_expires'] = $this->refreshTokenExpires;
        }

        $data['type'] = $user->getType();

        $event->setData($data);
    }
}