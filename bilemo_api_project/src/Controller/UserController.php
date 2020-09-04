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



/**
 * 
 * @Route("/api/v1")
 */
class UserController extends AbstractController
{
    /**
     * 
     * @Route("/users/{id}", name="show_user", methods={"GET"})
     */
    public function show($id, UserRepository $userRepository)
    {
        $user = $userRepository->find($id);
        if (!is_null($user)) {
            if ($user->getCustomer() === $this->getUser()) {
                return $this->json($user, 200, [
                    'Cache-Control' => 'public',
                    'maxage' => 3600,
                    'must-revalidate' => true
                ], ['groups' => 'user_read']);
            } else {
                $data = "Access denied";
                return $this->json($data, 403);
            }
        }
        $data = "Ressource not found";
        return $this->json($data, 404);
    }

    /**
     * @Route("/users/{page<\d+>?1}", name="list_user", methods={"GET"})
     */
    public function index(Request $request, UserRepository $userRepository)
    {
        $page = $request->query->get('page');
        if (is_null($page) || $page < 1) {
            $page = 1;
        }

        return $this->json($userRepository->findAllCustomerUsers(
            $this->getUser(),
            $page,
            $this->getParameter('limit')
        ), 200, [
            'Cache-Control' => 'public',
            'maxage' => 3600,
            'must-revalidate' => true
        ], [
            'groups' => 'user_read'
        ]);
    }

    /**
     * @Route("/users", name="add_user", methods={"POST"})
     */
    public function new(Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager, ValidatorInterface $validator, CustomerRepository $customerRepository)
    {
        $data = $request->getContent();
        $user = $serializer->deserialize($data, User::class, 'json', ['groups' => 'user_read']);
        $customer = $this->getUser();

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
