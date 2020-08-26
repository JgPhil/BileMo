<?php

namespace App\Controller;

use DateTime;
use App\Entity\Phone;
use App\Repository\PhoneRepository;
use Doctrine\ORM\Mapping\Annotation;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;



/**
 * @Route("/api")
 */
class PhoneController extends AbstractController
{
    private $encoder;
    private $normalizer;
    private $serializer;



    public function __construct()
    {
        $this->encoder = new JsonEncoder();
        $this->normalizer = new GetSetMethodNormalizer(null, null, null, null, null, $this->getDefaultContext());
        $this->serializer = new Serializer([$this->normalizer], [$this->encoder]);
    }

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
        $phone = $this->serializer->deserialize($request->getContent(), Phone::class, 'json');
        $errors = $validator->validate($phone);
        if (count($errors)) {
            $errors = $this->serializer->serialize($errors, 'json');
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


    /**
     * @Route("/phones/{id}", name="update_phone", methods={"PUT"})
     */
    public function update(Request $request, Phone $phone, ValidatorInterface $validator, EntityManagerInterface $entityManager)
    {
        $phoneUpdate = $entityManager->getRepository(Phone::class)->find($phone->getId());
        $data = json_decode($request->getContent());
        //Setters Construction
        foreach ($data as $key => $value) {
            if ($key && !empty($value)) {
                if (preg_match('/_/', $key)) { 
                    $value = new DateTime($value);
                    $key = str_replace("_", "", $key);
                    $key = str_replace(strrchr($key, "a"),"At", $key) ;
                }
                $name = ucfirst($key);
                $setter = 'set' . $name;
                $phoneUpdate->$setter($value);
            }
        }
        $errors = $validator->validate($phoneUpdate);
        if (count($errors)) {
            $errors = $this->serializer->serialize($errors, 'json');
            return new Response($errors, 500, [
                'Content-Type' => 'application/json'
            ]);
        }
        $entityManager->flush();
        $data = [
            'status' => 200,
            'message' => 'Le téléphone a bien été mis à jour'
        ];
        return new JsonResponse($data);
    }
}
