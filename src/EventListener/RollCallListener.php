<?php


namespace App\EventListener;


use App\Entity\RollCall;
use App\Entity\StudentPresence;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use OneSignal\OneSignal;

class RollCallListener
{
   private $api;

    public function __construct(OneSignal $api)
    {
        $this->api =$api;
    }

    public function postUpdate(LifecycleEventArgs $args)
    {
        die('coucou');
        $rollCall=$args->getObject();
        foreach ($rollCall->getStudentPresences() as $presence){

            if($presence==FALSE)
            {
                $this->api->notifications->add(([
                    'contents' => [
                        'fr' => 'Notification message'
                    ],
                    'included_segments' => ['All'],
                    'data' => ['foo' => 'bar'],
                    'large_icon' =>"ic_launcher_round.png",
                    // ..other options
                ]));

            }
        }
    }
}