<?php
/**
 * Created by PhpStorm.
 * User: salih
 * Date: 12.02.2019
 * Time: 17:31
 */

namespace App\Event;


use App\Entity\Task;
use Symfony\Component\EventDispatcher\Event;

class DoneEvent extends Event
{
    const NAME = 'my.event';
    /**
     * @var $task Task
     */
    private $task;

    /**
     * @return Task
     */
    public function getTask(): Task
    {
        return $this->task;
    }

    /**
     * @param Task $task
     */
    public function setTask(Task $task): void
    {
        $this->task = $task;
    }

}