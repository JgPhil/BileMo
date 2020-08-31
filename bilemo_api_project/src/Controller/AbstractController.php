<?php

namespace App\Controller;

use OpenApi\Annotations as Oa;

/**
 * @OA\Parameter(
 *      name="id",
 *      in="path",
 *      description="Ressource ID",
 *      required=true,
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
 */
class AbstractController extends \Symfony\Bundle\FrameworkBundle\Controller\AbstractController
{
}
