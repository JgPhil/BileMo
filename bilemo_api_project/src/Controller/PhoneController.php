<?php

namespace App\Controller;

use App\Entity\Phone;
use App\Repository\PhoneRepository;
use Doctrine\ORM\Mapping\Annotation;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;



/**
 * @Route("/api")
 */
class PhoneController extends AbstractController
{

    /**
     * @Route("/phones/{id}", name="show_phone", methods={"GET"})
     */
    public function show($id, PhoneRepository $phoneRepository)
    {
        $phone = $phoneRepository->find($id);
        return $this->json($phone, 200, [], ['groups' => 'show']);
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
    public function new(Request $request, EntityManagerInterface $entityManager, ValidatorInterface $validator)
    {
        $encoder = new JsonEncoder();

        // all callback parameters are optional (you can omit the ones you don't use)
        $dateCallback = function ($innerObject, $outerObject, string $attributeName, string $format = null, array $context = []) {
            return $innerObject instanceof \DateTime ? $innerObject->format(\DateTime::ISO8601) : '';
        };

        $defaultContext = [
            AbstractNormalizer::CALLBACKS => [
                'createdAt' => $dateCallback,
                'releasedAt' => $dateCallback
            ],
        ];

        $normalizer = new GetSetMethodNormalizer(null, null, null, null, null, $defaultContext);

        $serializer = new Serializer([$normalizer], [$encoder]);

        $phone = $serializer->deserialize($request->getContent(), Phone::class, 'json');
        $errors = $validator->validate($phone);
        if (count($errors)) {
            $errors = $serializer->serialize($errors, 'json');
            return new Response($errors, 500, [
                'Content-Type' => 'application/json'
            ]);
        }
        $entityManager->persist($phone);
        $entityManager->flush();
        $data = [
            'status' => 201,
            'message' => 'Le téléphone a bien été ajouté'
        ];
        return $this->json($data, 201);
    }
}
