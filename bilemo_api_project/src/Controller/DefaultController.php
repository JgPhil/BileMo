<?php

namespace App\Controller;

use JMS\Serializer\SerializationContext;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


use OpenApi\Annotations as Oa;

/**
 * @OA\Parameter(
 *      name="id",
 *      in="path",
 *      description="Ressource id property",
 *      required=true,
 *      @OA\Schema(type="integer")
 * )
 * 
 * @OA\Parameter(
 *      name="username",
 *      in="path",
 *      description="Ressource username property",
 *      required=true,
 *      @OA\Schema(type="string")
 * )
 * 
 * @OA\Parameter(
 *      name="page",
 *      in="query",
 *      description="the current page",
 *      required=false,
 *      @OA\Schema(type="integer")
 * )
 * 
 * @OA\Response(
 *      response="NotFound",
 *      description="Ressource not found",
 *      @OA\JsonContent(@OA\Property(property="message", type="string", example="user does not exists"))
 * )
 * 
 * @OA\Response(
 *      response="Unauthorized",
 *      description="Access denied",
 *      @OA\JsonContent(@OA\Property(property="message", type="string", example="You do not have the permissions to access this endpoint"))
 * )
 * 
 * @OA\Response(
 *      response="BadRequest",
 *      description="Bad Request",
 *      @OA\JsonContent(@OA\Property(property="message", type="string", example="A certain field was missing in the body"))
 *  )
 * 
 * @OA\SecurityScheme(bearerFormat="JWT", type="apiKey", securityScheme="bearer", in="cookie", name="BEARER")
 * 
 */
class DefaultController extends AbstractController
{


    protected $successResponse;

    public function __construct()
    {
        $this->successResponse = new Response();
        $this->successResponse->setStatusCode(200);
        $this->successResponse->headers->set('Content-Type', 'application/json');
        $this->successResponse->mustRevalidate();
        $this->successResponse->setMaxAge(3600);
    }
}
