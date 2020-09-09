<?php

namespace App\Controller;

use DateTime;
use App\Entity\User;
use App\Entity\Phone;
use App\Service\EntityUpdater;
use App\Service\HTTPCacheControl;
use App\Service\PageFetcher;
use App\Service\RequestManager;
use OpenApi\Annotations as Oa;
use Doctrine\ORM\EntityManager;
use JMS\Serializer\SerializerInterface;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializationContext;
use Symfony\Component\Validator\Validation;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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
    protected $HTTPCacheControl;


    protected $entityManager;

    protected $serializer;

    protected $requestManager;

    protected $entityUpdater;

    public function __construct( EntityManagerInterface $entityManager, SerializerInterface $serializer)
    {
        $this->entityUpdater = new EntityUpdater;       // Service which handles Entity update on PUT requests
        $this->requestManager = new RequestManager();   // Service which handles Cache-control && Request Parameters
        $this->entityManager = $entityManager;  
        $this->serializer = $serializer;
    }   
}
