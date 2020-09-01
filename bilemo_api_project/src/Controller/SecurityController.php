<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use OpenApi\Annotations as OA;


/**
 * @Route("/api/v1")
 */
class SecurityController extends AbstractController
{
    /**
     * @OA\Post(
     *      path="/login",
     *      tags={"Security"},
     *      @OA\Parameter(
     *          name="username",
     *          in="query",
     *          description="customer username",
     *          required=true,
     *          @OA\Schema(type="string")
     *      ),
     *      @OA\Parameter(
     *          name="password",
     *          in="query",
     *          description="customer password",
     *          required=true,
     *          @OA\Schema(type="string")
     *      ),
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