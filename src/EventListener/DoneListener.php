<?php
/**
 * Created by PhpStorm.
 * User: salih
 * Date: 12.02.2019
 * Time: 16:04
 */

namespace App\EventListener;

use App\Event\DoneEvent;
use Symfony\Bridge\Twig\TwigEngine;

class DoneListener
{
    /**
     * @var \Swift_Mailer $mailer
     */
    protected $mailer;

    /**
     * @var TwigEngine $twigEngine
     */
    protected $twigEngine;

    public function __construct(TwigEngine $twigEngine, \Swift_Mailer $mailer)
    {
        $this->twigEngine = $twigEngine;
        $this->mailer = $mailer;
    }

    public function doneNotify(DoneEvent $doneEvent)
    {
        $message = (new \Swift_Message('Congratulations!'))
            ->setFrom('send@example.com')
            ->setTo('salihbasakk@gmail.com')
            ->setBody(
                $this->twigEngine->render(
                    'emails/donetask.html.twig',
                    ['doneEvent' => $doneEvent]
                ),
                'text/html'
            )
        ;

        $this->mailer->send($message);

    }
}