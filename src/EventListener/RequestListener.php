<?php
/**
 * Created by PhpStorm.
 * User: salih
 * Date: 15.02.2019
 * Time: 23:20
 */

namespace App\EventListener;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

class RequestListener
{
    /**
     * @var \MongoClient $mongoClient
     */
    protected $mongoClient;

    /**
     * @var Request $request
     */
    protected $request;

    public function __construct(\MongoClient $mongoClient)
    {
        $this->mongoClient = $mongoClient;
    }

    public function listenRequest(FilterResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $request = $event->getRequest();
        $response = $event->getResponse();

        $data = [
            "url" => $request->getRequestUri(),
            "request_headers" => json_encode($request->headers->all()),
            "response_headers" => json_encode($response->headers->all()),
            "request_content" => $request->getContent(),
            "response_content" => $response->getContent(),
            "datetime" =>new \MongoDate(),
            "client_ip" => $request->getClientIp(),
        ];

        $this->mongoClient->testDb->testCollection->insert($data);

    }


}