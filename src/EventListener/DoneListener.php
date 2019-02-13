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
    public function doneNotify(DoneEvent $doneEvent)
    {
        //mail
        var_dump($doneEvent); die;
    }

}