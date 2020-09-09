<?php

namespace App\Controller;

use DateTime;
use App\Entity\User;
use App\Entity\Customer;
use OpenApi\Annotations as OA;
use App\Repository\UserRepository;
use Doctrine\ORM\Mapping\Annotation;
use App\Repository\CustomerRepository;
use App\Controller\DefaultController;
use JMS\Serializer\SerializerInterface;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializationContext;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Hateoas\Serializer\SerializerInterface as SerializerSerializerInterface;



/**
 * 
 * @Route("/api/v1")
 */
class UserController extends DefaultController
{

    /**
     * @OA\Get(
     *      path="/users/{id}",
     *      security={"bearer"},
     *      tags={"Users"},
     *          @OA\Parameter(ref="#/components/parameters/id"),
     *      @OA\Response(
     *         response="200",
     *         description="Show a user ressource",
     *         @OA\JsonContent(ref="#/components/schemas/User"),
     *      ),
     *      @OA\Response(response="403",ref="#/components/responses/Unauthorized"),
     *      @OA\Response(response="404",ref="#/components/responses/NotFound"), 
     * ) 
     * 
     * @Route("/users/{id}", name="show_user", methods={"GET"})
     */
    public function show($id, UserRepository $userRepository)
    {
        $user = $userRepository->find($id);
        if (!is_null($user)) {
            if ($user->getCustomer() === $this->getUser()) {
                return $this->requestManager->successResponseWithCache()->setContent($this->serializer->serialize($user, 'json'));
            } else {
                return $this->json("Access denied", 403);
            }
        }
        return $this->json("Ressource not found", 404);
    }

    /**
     * @OA\Get(
     *      path="/users",
     *      security={"bearer"},
     *      tags={"Users"},
     *      @OA\Parameter(ref="#/components/parameters/page"),
     *      @OA\Response(
     *          response="200",
     *          description="List of users ressources",
     *          @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/User"))
     *      )
     * )
     * @Route("/users/{page<\d+>?1}", name="list_user", methods={"GET"})
     */
    public function index(Request $request, UserRepository $userRepository)
    {
        $users = $userRepository->findAllCustomerUsers($this->getUser(), $this->requestManager->getPage($request), $this->getParameter('limit'))->getIterator();
        return $this->requestManager->successResponseWithCache()->setContent($this->serializer->serialize($users, 'json'));
    }

    /**
     * @OA\Post(
     *      path="/users",
     *      security={"bearer"},
     *      tags={"Users"},
     *      @OA\Response(
     *          response="201",
     *          description="New user ressource created",
     *          @OA\JsonContent(@OA\Property(property="message", type="string", example="New user ressource created"))
     *       ),
     *      @OA\Response(response="400",ref="#/components/responses/BadRequest")
     * )
     * 
     * @Route("/users", name="add_user", methods={"POST"})
     */
    public function new(Request $request, ValidatorInterface $validator)
    {
        $user = $this->serializer->deserialize($request->getContent(), User::class, 'json');
        $customer = $this->getUser();
        $errors = $validator->validate($user);
        if (count($errors)) {
            $errors = $this->serializer->serialize($errors, 'json');
            return new Response($errors, 400, [
                'Content-Type' => 'application/json'
            ]);
        }
        $this->entityManager->persist($user);
        $customer->addUser($user);
        $this->entityManager->flush();
        return $this->json($user, 201);
    }


    /**
     * @OA\Put(
     *      path="/users/{id}",
     *      security={"bearer"},
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
     * 
     * @Route("/users/{id}", name="update_user", methods={"PUT"})
     */
    public function update(Request $request, User $user, ValidatorInterface $validator)
    {
        if ($user->getCustomer() === $this->getUser()) {
            $data = json_decode($request->getContent());
            $user = $this->updateUserData($user, $data);
            $errors = $validator->validate($user);
            if (count($errors)) {
                $errors = $this->serializer->serialize($errors, 'json');
                return new Response($errors, 400, ['Content-Type' => 'application/json']);
            }
            $this->entityManager->flush();
            return $this->json($user);
        } else {
            return $this->json(['message' => 'Access denied'], 403);
        }
    }


    /**
     * @OA\Delete(
     *      path="/users/{id}",
     *      security={"bearer"},
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
    public function delete(User $user)
    {
        if ($user->getCustomer() === $this->getUser()) {
            $this->entityManager->remove($user);
            $this->entityManager->flush();
            return new Response(null, 204);
        } else {
            return $this->json(['message' => 'Access denied'], 403);
        }
    }

    private function updateUserData(User $user, $data): User
    {
        return $user = $this->entityUpdater->formatAndUpdate($user, $data);
    }
}
