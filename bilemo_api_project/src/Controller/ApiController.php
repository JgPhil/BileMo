<?php

namespace App\Controller;

use App\Repository\PhoneRepository;
use App\Controller\DefaultController;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

use OpenApi\Annotations as OA;


class ApiController extends DefaultController
{
    /**
     * @Route("/api/v1", name="api")
     */
    public function index()
    {
    }
}
