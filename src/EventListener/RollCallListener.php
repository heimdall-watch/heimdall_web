<?php


namespace App\EventListener;


use OneSignal\OneSignal;

class RollCallListener
{
   private $api;

    public function __construct(OneSignal $api)
    {
        $this->api =$api;
    }

    public function postUpdate($args)
    {
        // TODO ?
//        die('coucou');
//        $rollCall=$args->getObject();
//        foreach ($rollCall->getStudentPresences() as $presence){
//
//            if($presence==FALSE)
//            {
//                $this->api->notifications->add(([
//                    'contents' => [
//                        'fr' => 'Notification message'
//                    ],
//                    'included_segments' => ['All'],
//                    'data' => ['foo' => 'bar'],
//                    'large_icon' =>"ic_launcher_round.png",
//                    // ..other options
//                ]));
//
//            }
//        }
    }
}