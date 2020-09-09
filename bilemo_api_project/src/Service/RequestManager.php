<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;



class RequestManager
{


    public function successResponseWithCache($ttl): Response
    {
        $successResponse = $this->successResponseWithJsonType();        
        return $this->setCache($successResponse, $ttl);
    }

    public function successResponseWithJsonType()
    {
        $successResponse = new Response();
        $successResponse->headers->set('Content-Type', 'application/json');
        $successResponse->setStatusCode(200);
        return $successResponse;
    }

    public function setCache(Response $response, $ttl): Response
    {
        $response->setPublic();
        $response->mustRevalidate();
        $response->setMaxAge($ttl);
        return $response;
    }

    public function getPage(Request $request): int
    {
        $page = $request->query->get('page');
        if (is_null($page) || $page < 1) {
            $page = 1;
        }
        return $page;
    }
}
