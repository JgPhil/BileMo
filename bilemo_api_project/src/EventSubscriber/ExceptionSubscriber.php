<?php

namespace App\EventSubscriber;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class ExceptionSubscriber implements EventSubscriberInterface
{
    public function onKernelException(ExceptionEvent $event)
    {

        $response = new JsonResponse();
        /* 
        $exception = $event->getThrowable();
        if ($exception instanceof HttpExceptionInterface) {
            $data = [
                'message' => $exception->getMessage()
            ];
            $response->setData($data);
            $response->setStatusCode($exception->getStatusCode());
            $response->headers->replace($exception->getHeaders());
        } else {
            $response->setStatusCode(Response::HTTP_BAD_REQUEST);
            $response->setJson("");
            $response->setData([
                'message' => $exception->getMessage()
            ]);
        } */
        $response->setStatusCode(Response::HTTP_BAD_REQUEST);
        $response->setJson("");
        $response->setData([
            'message' => "Bad Request"
        ]);
        $event->setResponse($response);
    }

    public static function getSubscribedEvents()
    {
        return [
            'kernel.exception' => 'onKernelException',
        ];
    }
}
