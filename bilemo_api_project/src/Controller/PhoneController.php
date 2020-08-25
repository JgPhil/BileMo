<?php

namespace App\Controller;

use App\Entity\Phone;
use App\Repository\PhoneRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;



/**
 * @Route("/api")
 */
class PhoneController extends AbstractController
{

    /**
     * @Route("/phones/{id}", name="show_phone", methods={"GET"})
     */
    public function show(Phone $phone, PhoneRepository $phoneRepository)
    {
        return $this->json($phoneRepository->find($phone->getId()), 200, [], ['groups' => 'show']);
    }

    /**
     * @Route("/phones/{page<\d+>?1}", name="list_phone", methods={"GET"})
     */
    public function index(Request $request, PhoneRepository $phoneRepository)
    {
        $page = $request->query->get('page');
        if (is_null($page) || $page < 1) {
            $page = 1;
        }
        return $this->json($phoneRepository->findAllPhones($page, $this->getParameter('limit')), 200, [], ['groups' => 'list']);
    }

    /**
     * @Route("/phones", name="add_phone", methods={"POST"})
     */
    public function new(Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager)
    {
        $phone = $serializer->deserialize($request->getContent(), Phone::class, 'json');
        $entityManager->persist($phone);
        $entityManager->flush();
        $data = [
            'status' => 201,
            'message' => 'Le téléphone a bien été ajouté'
        ];
        return $this->json($data, 201);
    }
}
