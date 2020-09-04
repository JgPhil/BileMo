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
    public function show($id, PhoneRepository $phoneRepository, SerializerInterface $serializer)
    {
        $phone = $phoneRepository->find($id);


        if (!is_null($phone)) {
            return $this->successResponse->setContent($serializer->serialize($phone, 'json'));
        }
        $data = "Ressource not found";
        return $this->json($data, 404);
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
    public function index(Request $request, PhoneRepository $phoneRepository, SerializerInterface $serializer)
    {
        $page = $request->query->get('page');
        if (is_null($page) || $page < 1) {
            $page = 1;
        }
        $phones = $phoneRepository->findAllPhones($page, $this->getParameter('limit'))->getIterator();
        return $this->successResponse->setContent($serializer->serialize($phones, 'json'));
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
