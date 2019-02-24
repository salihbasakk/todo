<?php
/**
 * Created by PhpStorm.
 * User: salih
 * Date: 19.02.2019
 * Time: 14:09
 */

namespace App\EventListener;

use App\Event\MemcacheEvent;

class MemcacheListener
{
    /** @var \Memcached*/
    private $memcached;

    /**
     * MemcacheListener constructor.
     * @param \Memcached $memcached
     */
    public function __construct(\Memcached $memcached)
    {
        $this->memcached = $memcached;
    }

    public function clearMemcache(MemcacheEvent $memcacheEvent)
    {
        $memKey = $memcacheEvent->getMemcacheKey();

        $this->memcached->delete($memKey);
    }
}