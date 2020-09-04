<?php

namespace App\Controller;

use App\Controller\DefaultController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use OpenApi\Annotations as OA;


/**
 * @Route("/api/v1")
 */
class SecurityController extends DefaultController
{
    /**
     * @OA\Post(
     *      path="/login",
     *      tags={"Security"},
     *      @OA\RequestBody(
     *    		@OA\MediaType(
     *    			mediaType="application/json",
     *    			@OA\Schema(
     *    				 @OA\Property(property="username",
     *    					type="string",
     *    					example="",
     *    					description=""
     *    				),
     *    				 @OA\Property(property="password",
     *    					type="string",
     *    					example="",
     *    					description=""
     *    				)
     *    			),
     *    		),
     *    	),
     *      @OA\Response(
     *          response=204,
     *          description="Authentication success")
     * )
     * 
     * @Route("/login", name="login", methods={"POST"})
     */
    public function login()
    {
    }
}
