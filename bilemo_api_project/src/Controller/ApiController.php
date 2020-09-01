<?php

namespace App\Controller;

use App\Repository\PhoneRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

use OpenApi\Annotations as OA;


class ApiController extends AbstractController
{
    /**
     * @Route("/api/v1", name="api")
     */
    public function index()
    {
    }
}
