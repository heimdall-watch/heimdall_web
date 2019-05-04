<?php

namespace App\EventListener;

use App\Entity\Student;
use App\Entity\User;
use Gesdinet\JWTRefreshTokenBundle\Event\RefreshEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Psr\Log\LoggerInterface;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

class ApiAuthenticationListener
{
    private $logger;
    private $uploaderHelper;
    private $tokenExpires;
    private $refreshTokenExpires;

    public function __construct($tokenExpires, $refreshTokenExpires, LoggerInterface $logger, UploaderHelper $uploaderHelper)
    {
        $this->logger = $logger;
        $this->uploaderHelper = $uploaderHelper;
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

        $data['user'] = ['username' => $user->getUsername(), 'type' => $user->getType(), 'lastLogin' => $user->getLastLogin()];
        if ($user instanceof Student) {
            $data['user']['photo'] = $this->uploaderHelper->asset($user, 'photoFile');
        }

        $event->setData($data);
    }

    public function onTokenRefresh(RefreshEvent $event)
    {
        $this->logger->debug(sprintf("User %s has refreshed his token with: %s", $event->getRefreshToken()->getUsername(), $event->getRefreshToken()->getRefreshToken()));
    }
}