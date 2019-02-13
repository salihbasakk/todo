<?php
/**
 * Created by PhpStorm.
 * User: salih
 * Date: 12.02.2019
 * Time: 16:04
 */

namespace App\EventListener;

use App\Event\DoneEvent;

class DoneListener
{
    public function doneNotify(DoneEvent $doneEvent, \Swift_Mailer $mailer)
    {
        $message = (new \Swift_Message('Congratulations!'))
            ->setFrom('send@example.com')
            ->setTo('salihbasakk@gmail.com')
            ->setBody(
                $this->renderView(
                    'emails/donetask.html.twig',
                    ['doneEvent' => $doneEvent]
                ),
                'text/html'
            )
        ;

        $mailer->send($message);

    }

}