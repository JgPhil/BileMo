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
     * @Route("/users/{id}", name="show_user", methods={"GET"})
     */
    public function show($id, UserRepository $userRepository)
    {
        $user = $userRepository->find($id);
        return $this->json($user, 200, [], ['groups' => 'user_read']);
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
        return $this->json($userRepository->findAllUsers($page, $this->getParameter('limit')), 200, [], ['groups' => 'user_read']);
    }

    /**
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
     * @Route("/users/{id}", name="update_user", methods={"PUT"})
     */
    public function update(Request $request, User $user,SerializerInterface $serializer, ValidatorInterface $validator, EntityManagerInterface $entityManager)
    {
        $userUpdate = $entityManager->getRepository(User::class)->find($user->getId());
        $data = json_decode($request->getContent());
        //Setters Construction
        foreach ($data as $key => $value) {
            if ($key !== "id") {
                if ($key === "createdAt") {
                    $value = new DateTime($value);
                    $key = "createdAt";
                }
                $name = ucfirst($key);
                $setter = 'set' . $name;
                $userUpdate->$setter($value);
            }
        }
        $errors = $validator->validate($userUpdate);
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
    }


    /**
     * @Route("/users/{id}", name="delete_user", methods={"DELETE"})
     */
    public function delete(User $user, EntityManagerInterface $entityManager)
    {
        $entityManager->remove($user);
        $entityManager->flush();
        return new Response(null, 204);
    }
}
