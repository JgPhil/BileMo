<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;



class RequestManager
{


    public function successResponseWithCache(): Response
    {
        $successResponse = new Response();
        $successResponse->setStatusCode(200);
        $successResponse->headers->set('Content-Type', 'application/json');
        return $this->setCache($this->successResponse);
    }

    public function setCache(Response $response): Response
    {
        $response->setPublic();
        $response->mustRevalidate();
        $response->setMaxAge(3600);
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
