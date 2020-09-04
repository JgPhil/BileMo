<?php

namespace App\Controller;

use DateTime;
use App\Entity\Phone;
use App\Repository\PhoneRepository;
use Doctrine\ORM\Mapping\Annotation;
use App\Repository\CustomerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;



/**
 * @Route("/api/v1")
 */
class PhoneController extends AbstractController
{
    /**
     * @Route("/phones/{id}", name="show_phone", methods={"GET"})
     */
    public function show($id, PhoneRepository $phoneRepository)
    {
        $phone = $phoneRepository->find($id);
        if (!is_null($phone)) {
            return $this->json($phone, 200, [
                'Cache-Control' => 'public',
                'maxage' => 3600,
                'must-revalidate' => true
            ]);
        }
        $data = "Ressource not found";
        return $this->json($data, 404);
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
        return $this->json(
            $phoneRepository->findAllPhones($page, $this->getParameter('limit')),
            200,
            [
                'Cache-Control' => 'public',
                'maxage' => 3600,
                'must-revalidate' => true
            ]
        );
    }

    /**
     * @Route("/phones", name="add_phone", methods={"POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function new(Request $request, EntityManagerInterface $entityManager, SerializerInterface $serializer, ValidatorInterface $validator)
    {
        $phone = $serializer->deserialize($request->getContent(), Phone::class, 'json');
        $errors = $validator->validate($phone);
        if (count($errors)) {
            $errors = $serializer->serialize($errors, 'json');
            return new Response($errors, 400, [
                'Content-Type' => 'application/json'
            ]);
        }
        $entityManager->persist($phone);
        $entityManager->flush();
        $data = [
            'message' => 'Le téléphone a bien été ajouté'
        ];
        return $this->json($data, 201);
    }


    /**
     * @Route("/phones/{id}", name="update_phone", methods={"PUT"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function update(Request $request, Phone $phone, ValidatorInterface $validator, SerializerInterface $serializer, EntityManagerInterface $entityManager)
    {
        $phoneUpdate = $entityManager->getRepository(Phone::class)->find($phone->getId());
        $data = json_decode($request->getContent());
        //Setters Construction
        foreach ($data as $key => $value) {
            if ($key !== "id") {
                if ($key === "releasedAt") {
                    $value = new DateTime($value);
                    $key = "ReleasedAt";
                }
                $name = ucfirst($key);
                $setter = 'set' . $name;
                $phoneUpdate->$setter($value);
            }
        }
        $errors = $validator->validate($phoneUpdate);
        if (count($errors)) {
            $errors = $serializer->serialize($errors, 'json');
            return new Response($errors, 400, [
                'Content-Type' => 'application/json'
            ]);
        }
        $entityManager->flush();
        $data = [
            'message' => 'Le téléphone a bien été mis à jour'
        ];
        return new JsonResponse($data);
    }


    /**
     * @Route("/phones/{id}", name="delete_phone", methods={"DELETE"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function delete(Phone $phone, EntityManagerInterface $entityManager)
    {
        $role = $this->getUser()->getRoles();
        if ($role[0] !== 'ROLE_ADMIN') {
            $data = [
                'message' => 'Access denied'
            ];
            return $this->json($data, 403);
        }
        $entityManager->remove($phone);
        $entityManager->flush();
        return new Response(null, 204);
    }
}
