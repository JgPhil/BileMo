<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\Response;

class HTTPCacheControl
{
    private $successResponse;

    public function __construct()
    {
        $this->successResponse = new Response();
        $this->successResponse->setStatusCode(200)
            ->headers->set('Content-Type', 'application/json');
    }


    public function successResponseWithCache(): Response
    {
       return $this->setCache($this->successResponse);
    }

    public function setCache(Response $response): Response
    {
        $response->setPublic();
        $response->mustRevalidate();
        $response->setMaxAge(3600);
        return $response;
    }
}
