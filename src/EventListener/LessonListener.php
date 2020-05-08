<?php


namespace App\EventListener;


use App\Entity\Lesson;
use OneSignal\OneSignal;
use Psr\Log\LoggerInterface;

class LessonListener
{
    private $api;
    private $logger;

    public function __construct(OneSignal $api, LoggerInterface $logger)
    {
        $this->api = $api;
        $this->logger = $logger;
    }

    public function postPersist(Lesson $lesson)
    {
            foreach ($lesson->getStudentPresences() as $studentPresence) {
                try {
                    if (($studentPresence->getPresent() === false || $studentPresence->getLate() !== null) && !empty($studentPresence->getStudent()->getDevices())) {
                        $notif = [
                            'contents' => [
                                'en' => 'You are ' . ($studentPresence->getPresent() === false ? 'absent' : 'late') . ' to the class of ' . $lesson->getTeacher()->getFirstname() . ' ' . $lesson->getTeacher()->getLastName(),
                                'fr' => 'Vous êtes ' . ($studentPresence->getPresent() === false ? 'absent' : 'en retard') . ' au cours de ' . $lesson->getTeacher()->getFirstname() . ' ' . $lesson->getTeacher()->getLastName(),
                            ],
                            'include_player_ids' => $studentPresence->getStudent()->getDevices()
                        ];
                        $this->api->notifications->add($notif);
                        $this->logger->debug("Sending onesignal notif to " . $studentPresence->getStudent()->getUsername() . ' : ' . json_encode($notif));
                    }
                } catch (\Exception $e) {
                    $this->logger->error($e->getMessage());
                }
            }
    }
}