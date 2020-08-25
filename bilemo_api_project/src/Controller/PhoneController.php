<?php

namespace App\Controller;

use App\Repository\PhoneRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/api/phones")
 */
class PhoneController extends AbstractController
{
    /**
     * @Route("/{page<\d+>?1}", name="list_phone", methods={"GET"})
     */
    public function index(Request $request, PhoneRepository $phoneRepository, SerializerInterface $serializer)
    {
        $page = $request->query->get('page');
        $limit = 10;
        return $this->json($phoneRepository->findAllPhones($page, $limit), 200);
    }



}
