<?php

namespace App\Controller;

use App\Repository\PhoneRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ApiController extends AbstractController
{
    /**
     * @Route("/api", name="api")
     */
    public function index(PhoneRepository $phoneRepo)
    {
        $data = $phoneRepo->findAll();

        return $this->json($data, 200);
    }
}
