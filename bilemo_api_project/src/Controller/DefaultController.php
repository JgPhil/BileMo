<?php

namespace App\Controller;

use App\Entity\User;
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
        $this->successResponse = $this->cachedSuccessResponseFactory();
    }

    protected function updateUserData(User $user, $data)
    {
        foreach ($data as $key => $value) {
            if ($key !== "id") {
                if ($key === "createdAt" ) {
                    $value = new \DateTime($value);
                    $key = "createdAt";
                }
                $name = ucfirst($key);
                $setter = 'set' . $name;
                $user->$setter($value);
            }
        }
        return $user;
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
