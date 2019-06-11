<?php

namespace App\EventSubscriber;

use App\Entity\Student;
use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\PreSerializeEvent;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

class SerializeSubscriber implements EventSubscriberInterface
{
    /**
     * @var UploaderHelper
     */
    private $uploaderHelper;

    public function __construct(UploaderHelper $uploaderHelper)
    {
        $this->uploaderHelper = $uploaderHelper;
    }

    public function onStudentPreSerialize(PreSerializeEvent $event)
    {
        /** @var Student $student */
        $student = $event->getObject();
        $fullPhotoUrl = $this->uploaderHelper->asset($student, 'photoFile');
        if ($student->getPhoto() != $fullPhotoUrl) {
            $student->setPhoto($fullPhotoUrl);
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
        ];
    }
}
