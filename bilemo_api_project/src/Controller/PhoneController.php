<?php

namespace App\Controller;

use App\Entity\Phone;
use App\Repository\PhoneRepository;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;



/**
 * @Route("/api/phones")
 */
class PhoneController extends AbstractController
{

    /**
     * @Route("/{id}", name="show_phone", methods={"GET"})
     */
    public function show(Phone $phone, PhoneRepository $phoneRepository)
    {
        return $this->json($phoneRepository->find($phone->getId()), 200, [], ['groups' => 'show']);
    }

    /**
     * @Route("/{page<\d+>?1}", name="list_phone", methods={"GET"})
     */
    public function index(Request $request, PhoneRepository $phoneRepository)
    {
        $page = $request->query->get('page');
        if (is_null($page) || $page < 1) {
            $page = 1;
        }
        return $this->json($phoneRepository->findAllPhones($page, $this->getParameter('limit')), 200, [], ['groups' => 'list']);
    }
}
