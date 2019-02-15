<?php

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
            ->setFrom('crawler.enuygun@gmail.com')
            ->setTo($doneEvent->getTask()->getUser()->getEmail())
            ->setBody(
                $this->twigEngine->render(
                    'emails/email.html.twig'
                ),
                'text/html'
            )
        ;
        $this->mailer->send($message);
    }
}
