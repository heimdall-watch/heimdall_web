<?php


namespace App\EventListener;


use App\Entity\RollCall;
use OneSignal\OneSignal;
use Psr\Log\LoggerInterface;

class RollCallListener
{
    private $api;
    private $logger;

    public function __construct(OneSignal $api, LoggerInterface $logger)
    {
        $this->api = $api;
        $this->logger = $logger;
    }

    public function postPersist(RollCall $rollCall)
    {
        try {
            foreach ($rollCall->getStudentPresences() as $studentPresence) {
                if ($studentPresence->getPresent() === false || $studentPresence->getLate() !== null && !empty($studentPresence->getStudent()->getDevices())) {
                    $notif = [
                        'contents' => [
                            'en' => 'You are ' . ($studentPresence->getPresent() === false ? 'absent' : 'late') . ' to the class of ' . $rollCall->getTeacher()->getFirstname() . ' ' . $rollCall->getTeacher()->getLastName(),
                            'fr' => 'Vous êtes ' . ($studentPresence->getPresent() === false ? 'absent' : 'en retard') . ' au cours de ' . $rollCall->getTeacher()->getFirstname() . ' ' . $rollCall->getTeacher()->getLastName(),
                        ],
                        'include_player_ids' => $studentPresence->getStudent()->getDevices()
                    ];
                    $this->api->notifications->add($notif);
                    $this->logger->debug("Sending onesignal notif to " . $studentPresence->getStudent()->getUsername() . ' : ' . json_encode($notif));
                }
            }
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
        }
    }
}