<?php

namespace App\Controller;

use App\Entity\Customer;
use DateTime;
use App\Entity\User;
use App\Repository\CustomerRepository;
use App\Repository\UserRepository;
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
use OpenApi\Annotations as OA;



/**
 * 
 * @Route("/api")
 */
class UserController extends AbstractController
{

    protected $encoder;
    protected $normalizer;
    protected $serializer;


    public function __construct()
    {
        $this->encoder = new JsonEncoder();
        $this->normalizer = new GetSetMethodNormalizer(null, null, null, null, null, $this->getDefaultContext());
        $this->serializer = new Serializer([$this->normalizer], [$this->encoder]);
    }



    /**
     * @OA\Get(
     *      path="/users/{id}",
     *      tags={"Users"},
     *          @OA\Parameter(ref="#/components/parameters/id"),
     *      @OA\Response(
     *         response="200",
     *         description="Show a user",
     *         @OA\JsonContent(ref="#/components/schemas/User"),
     *      ),
     *      @OA\Response(response="403",ref="#/components/responses/Unauthorized"),
     *      @OA\Response(response="404",ref="#/components/responses/NotFound")
     * ) 
     * 
     * @Route("/users/{id}", name="show_user", methods={"GET"})
     */
    public function show($id, UserRepository $userRepository)
    {
        $user = $userRepository->find($id);
        if (!is_null($user)) {
            if ($user->getCustomer() === $this->getUser()) {
                return $this->json($user, 200, [], ['groups' => 'user_read']);
            } else {
                $data = "Access denied";
                return $this->json($data, 403);
            }
        }
        $data = "Ressource not found";
        return $this->json($data, 404);
    }

    /**
     * @OA\Get(
     *      path="/users",
     *      tags={"Users"},
     *      @OA\Parameter(ref="#/components/parameters/page"),
     *      @OA\Response(
     *          response="200",
     *          description="List of users",
     *          @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/User"))
     *      )
     * )
     * @Route("/users/{page<\d+>?1}", name="list_user", methods={"GET"})
     */
    public function index(Request $request, UserRepository $userRepository)
    {
        $page = $request->query->get('page');
        if (is_null($page) || $page < 1) {
            $page = 1;
        }
        return $this->json($userRepository->findAllCustomerUsers($this->getUser(), $page, $this->getParameter('limit')), 200, [], ['groups' => 'user_read']);
    }

    /**
     * @OA\Post(
     *      path="/users",
     *      tags={"Users"},
     *      @OA\Response(
     *          response="201",
     *          description="New user created",
     *          @OA\JsonContent(@OA\Property(property="message", type="string", example="New user created"))
     *       ),
     *      @OA\Response(response="400",ref="#/components/responses/BadRequest")
     * )
     * 
     * @Route("/users", name="add_user", methods={"POST"})
     */
    public function new(Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager, ValidatorInterface $validator, CustomerRepository $customerRepository)
    {
        $data = $request->getContent();
        $user = $serializer->deserialize($data, User::class, 'json', ['groups' => 'user_read']);
        $customer = $customerRepository->find(1);

        $errors = $validator->validate($user, [], ['groups' => 'user_read']);
        if (count($errors)) {
            $errors = $serializer->serialize($errors, 'json');
            return new Response($errors, 400, [
                'Content-Type' => 'application/json'
            ]);
        }
        $entityManager->persist($user);
        $customer->addUser($user);
        $entityManager->flush();
        $data = [
            'message' => 'L\'utilisateur a bien été ajouté'
        ];
        return $this->json($data, 201);
    }


    /**
     * @OA\Put(
     *      path="/users/{id}",
     *      tags={"Users"},
     *      @OA\Parameter(ref="#/components/parameters/id"),
     *      @OA\Response(
     *          response="200",
     *          description="User Update",
     *          @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/User"))
     *      ),
     *      @OA\Response(response="403",ref="#/components/responses/Unauthorized"),
     *      @OA\Response(response="400",ref="#/components/responses/BadRequest")
     * )
     * @Route("/users/{id}", name="update_user", methods={"PUT"})
     */
    public function update(Request $request, User $user, SerializerInterface $serializer, ValidatorInterface $validator, EntityManagerInterface $entityManager)
    {
        if ($user->getCustomer() === $this->getUser()) {
            $data = json_decode($request->getContent());
            foreach ($data as $key => $value) {
                if ($key !== "id") {
                    if ($key === "createdAt") {
                        $value = new DateTime($value);
                        $key = "createdAt";
                    }
                    $name = ucfirst($key);
                    $setter = 'set' . $name;
                    $user->$setter($value);
                }
            }
            $errors = $validator->validate($user);
            if (count($errors)) {
                $errors = $serializer->serialize($errors, 'json');
                return new Response($errors, 400, [
                    'Content-Type' => 'application/json'
                ]);
            }
            $entityManager->flush();
            $data = [
                'message' => 'L\'utilisateur a bien été mis à jour'
            ];
            return new JsonResponse($data);
        } else {
            $data = [
                'message' => 'Access denied'
            ];
            return $this->json($data, 403);
        }
    }


    /**
     * @OA\Delete(
     *      path="/users/{id}",
     *      tags={"Users"},
     *      @OA\Parameter(ref="#/components/parameters/id"),
     *      @OA\Response(
     *          response="204",
     *          description="Delete a User"
     *      ),
     *      @OA\Response(response="403",ref="#/components/responses/Unauthorized")
     * )
     * 
     * @Route("/users/{id}", name="delete_user", methods={"DELETE"})
     */
    public function delete(User $user, EntityManagerInterface $entityManager)
    {
        if ($user->getCustomer() === $this->getUser()) {
            $entityManager->remove($user);
            $entityManager->flush();
            return new Response(null, 204);
        } else {
            $data = [
                'message' => 'Access denied'
            ];
            return $this->json($data, 403);
        }
    }
}
