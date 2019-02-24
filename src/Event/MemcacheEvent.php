<?php
/**
 * Created by PhpStorm.
 * User: salih
 * Date: 19.02.2019
 * Time: 14:08
 */

namespace App\Event;


use Symfony\Component\EventDispatcher\Event;

class MemcacheEvent extends Event
{
    const NAME = 'memcache.clear';

    /**
     * @var string
     */
    private $memcacheKey;

    /**
     * @return string
     */
    public function getMemcacheKey(): string
    {
        return $this->memcacheKey;
    }

    /**
     * @param string $memcacheKey
     */
    public function setMemcacheKey(string $memcacheKey): void
    {
        $this->memcacheKey = $memcacheKey;
    }

}