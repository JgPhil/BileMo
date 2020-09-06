<?php

namespace App\Controller;

use App\Entity\Phone;
use App\Entity\User;
use DateTime;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


use OpenApi\Annotations as Oa;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;

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

    protected $entityManager;

    protected $validator;

    protected $serializer;

    public function __construct(EntityManagerInterface $entityManager, ValidatorInterface $validator, SerializerInterface $serializer)
    {
        $this->successResponse = $this->cachedSuccessResponseFactory();
        $this->entityManager = $entityManager;
        $this->validator = $validator;
        $this->serializer = $serializer;
    }    

    protected function getPage(Request $request)
    {
        $page = $request->query->get('page');
        if (is_null($page) || $page < 1) {
            $page = 1;
        }
        return $page;
    }

    protected function formatAndUpdate(object $entity, $data)
    {
        foreach ($data as $key => $value) {
            if ($key !== "id") {
                if ($key === 'createdAt' || $key ==='releasedAt') {
                    $value = new \DateTime($value);
                }
                $setter = "set" . ucfirst($key);
                $entity->$setter($value);
            }
        }
        return $entity;
    }

    private function cachedSuccessResponseFactory()
    {
        $response = new Response();
        $response->setStatusCode(200);
        $response->headers->set('Content-Type', 'application/json');
        $response->mustRevalidate();
        $response->setMaxAge(3600);
        return $response;
    }
}
