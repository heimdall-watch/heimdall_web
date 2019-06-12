<?php

namespace App\EventSubscriber;

use App\Entity\Student;
use App\Entity\StudentPresence;
use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\PreSerializeEvent;
use Symfony\Component\Routing\RouterInterface;

class SerializeSubscriber implements EventSubscriberInterface
{
    /**
     * @var RouterInterface
     */
    private $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function onStudentPreSerialize(PreSerializeEvent $event)
    {
        /** @var Student $student */
        $student = $event->getObject();
        if ($student instanceof Student && $student->getPhoto() != null && strstr($student->getPhoto(), '/') === false) {
            $student->setPhoto($this->router->generate('api_student_get_photo', ['id' => $student->getId()], RouterInterface::ABSOLUTE_URL));
        }
    }

    public function onStudentPresenceSerialize(PreSerializeEvent $event)
    {
        /** @var StudentPresence $studentPresence */
        $studentPresence = $event->getObject();
        if ($studentPresence instanceof StudentPresence && $studentPresence->getExcuseProof() != null && strstr($studentPresence->getExcuseProof(), '/') === false) {
            $studentPresence->setExcuseProof($this->router->generate('api_get_excuse_proof_photo', ['id' => $studentPresence->getId()], RouterInterface::ABSOLUTE_URL));
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            [
                'event' => 'serializer.pre_serialize',
                'method' => 'onStudentPreSerialize',
                'class' => Student::class,
            ],
            [
                'event' => 'serializer.pre_serialize',
                'method' => 'onStudentPresenceSerialize',
                'class' => StudentPresence::class,
            ],
        ];
    }
}
