<?php

namespace App\Controller;

use DateTime;
use App\Entity\Phone;
use App\Repository\PhoneRepository;
use Doctrine\ORM\Mapping\Annotation;
use App\Repository\CustomerRepository;
use App\Controller\DefaultController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Common\Persistence\ManagerRegistry;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use OpenApi\Annotations as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;



/**
 * @Route("/api/v1")
 */
class PhoneController extends DefaultController
{
    /**
     * @OA\Get(
     *      path="/phones/{id}",
     *      security={"bearer"},
     *      tags={"Phones"},
     *          @OA\Parameter(ref="#/components/parameters/id"),
     *      @OA\Response(
     *         response="200",
     *         description="Shows a Phone",
     *         @OA\JsonContent(ref="#/components/schemas/Phone"),
     *      ),
     *      @OA\Response(response="404",ref="#/components/responses/NotFound")
     * )
     * 
     * @Route("/phones/{id}", name="show_phone", methods={"GET"})
     */
    public function show($id, PhoneRepository $phoneRepository)
    {
        $phone = $phoneRepository->find($id);
        if (!is_null($phone)) {
            return $this->successResponse->setContent($this->serializer->serialize($phone, 'json'));
        }
        return $this->json(["message" => "Ressource not found"], 404);
    }

    /**
     * @OA\Get(
     *      path="/phones",
     *      security={"bearer"},
     *      tags={"Phones"},
     *      @OA\Parameter(ref="#/components/parameters/page"),
     *      @OA\Response(
     *          response="200",
     *          description="List of phones",
     *          @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Phone"))
     *      )
     * )
     * @Route("/phones/{page<\d+>?1}", name="list_phone", methods={"GET"})
     */
    public function index(Request $request, PhoneRepository $phoneRepository)
    {
        $page = $this->getPage($request);
        $phones = $phoneRepository->findAllPhones($page, $this->getParameter('limit'))->getIterator();
        return $this->successResponse->setContent($this->serializer->serialize($phones, 'json'));
    }

    /**
     * @OA\Post(
     *      path="/phones",
     *      security={"bearer"},
     *      tags={"Phones"},
     *      @OA\Response(
     *          response="201",
     *          description="New phone ressource created",
     *          @OA\JsonContent(@OA\Property(property="message", type="string", example="New phone ressource created"))
     *       ),
     *      @OA\Response(response="400",ref="#/components/responses/BadRequest")
     * )
     * 
     * @Route("/phones", name="add_phone", methods={"POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function new(Request $request, EntityManagerInterface $entityManager, ValidatorInterface $validator)
    {
        $phone = $this->serializer->deserialize($request->getContent(), Phone::class, 'json');
        $errors = $validator->validate($phone);
        if (count($errors)) {
            $errors = $this->serializer->serialize($errors, 'json');
            return new Response($errors, 400, [
                'Content-Type' => 'application/json'
            ]);
        }
        $entityManager->persist($phone);
        $entityManager->flush();
        return $this->json(['message' => 'Le téléphone a bien été ajouté'], 201);
    }


    /**
     * @OA\Put(
     *      path="/phones/{id}",
     *      security={"bearer"},
     *      tags={"Phones"},
     *      @OA\Parameter(ref="#/components/parameters/id"),
     *      @OA\Response(
     *          response="200",
     *          description="Phone Update",
     *          @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Phone"))
     *      ),
     *      @OA\Response(response="400",ref="#/components/responses/BadRequest")
     * )
     * 
     * @Route("/phones/{id}", name="update_phone", methods={"PUT"})
     */
    public function update(Request $request, Phone $phone, ValidatorInterface $validator)
    {
        $role = $this->getUser()->getRoles();
        if ($role[0] !== 'ROLE_ADMIN') {
            return $this->json(['message' => 'Access denied'], 403);
        }
        $data = json_decode($request->getContent());
        $phone = $this->updatePhoneData($phone, $data); 
        $errors = $validator->validate($phone);
        if (count($errors)) {
            $errors = $this->serializer->serialize($errors, 'json');
            return new Response($errors, 400, ['Content-Type' => 'application/json']);
        }
        $this->entityManager->flush();
        return new JsonResponse(['message' => 'Le téléphone a bien été mis à jour']);
    }


    /**
     * @OA\Delete(
     *      path="/phones/{id}",
     *      security={"bearer"},
     *      tags={"Phones"},
     *      @OA\Parameter(ref="#/components/parameters/id"),
     *      @OA\Response(
     *          response="204",
     *          description="Delete a Phone"
     *      ),
     *      @OA\Response(response="403",ref="#/components/responses/Unauthorized")
     * )
     * 
     * @Route("/phones/{id}", name="delete_phone", methods={"DELETE"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function delete(Phone $phone)
    {
        $role = $this->getUser()->getRoles();
        if ($role[0] !== 'ROLE_ADMIN') {
            return $this->json(['message' => 'Access denied'], 403);
        }
        $this->entityManager->remove($phone);
        $this->entityManager->flush();
        return new Response(null, 204);
    }


    private function updatePhoneData(Phone $phone, $data):Phone
    {
        return $phone = $this->formatAndUpdate($phone, $data); //DefaultController method with setters
    }
}
