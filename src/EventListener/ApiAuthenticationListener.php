<?php

namespace App\EventListener;

use App\Entity\Student;
use App\Entity\User;
use Gesdinet\JWTRefreshTokenBundle\Event\RefreshEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\Routing\RouterInterface;

class ApiAuthenticationListener
{
    private $logger;
    private $router;
    private $tokenExpires;
    private $refreshTokenExpires;

    public function __construct($tokenExpires, $refreshTokenExpires, LoggerInterface $logger, RouterInterface $router)
    {
        $this->logger = $logger;
        $this->router = $router;
        $this->tokenExpires = $tokenExpires * 1000;
        $this->refreshTokenExpires = $refreshTokenExpires * 1000;
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

        $now_milli = round(microtime(true) * 1000);
        $data['token_expires'] = $now_milli + $this->tokenExpires;
        if (\array_key_exists('refresh_token', $data)) {
            $data['refresh_token_expires'] = $now_milli + $this->refreshTokenExpires;
        }
        $data['onesignal_app_id'] = getenv('ONESIGNAL_APP_ID');

        $data['user'] = [
            'id' => $user->getId(),
            'type' => $user->getType(),
            'username' => $user->getUsername(),
            'email' => $user->getEmail(),
            'firstname' => $user->getFirstname(),
            'lastname' => $user->getLastname(),
            'last_login' => $user->getLastLogin()->format('c'),
        ];
        if ($user instanceof Student) {
            $data['user']['photo'] = $this->router->generate('api_student_get_photo', ['id' => $user->getId()], RouterInterface::ABSOLUTE_URL);
        }

        $event->setData($data);
    }

    public function onTokenRefresh(RefreshEvent $event)
    {
        $this->logger->debug(sprintf("User %s has refreshed his token with: %s", $event->getRefreshToken()->getUsername(), $event->getRefreshToken()->getRefreshToken()));
    }
}