<?php

namespace App\Controller;

use App\Entity\Customer;
use OpenApi\Annotations as OA;
use App\Repository\CustomerRepository;
use App\Controller\DefaultController;
use App\Repository\UserRepository;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;

/**
 * @Route("/api/v1")
 */
class CustomerController extends DefaultController
{

    /**
     * @OA\Get(
     *      path="/customers/{username}",
     *      security={"bearer"},
     *      tags={"Customers"},
     *          @OA\Parameter(ref="#/components/parameters/username"),
     *      @OA\Response(
     *         response="200",
     *         description="Show a customer ressource",
     *         @OA\JsonContent(ref="#/components/schemas/Customer")
     *      ),
     *      @OA\Response(response="403",ref="#/components/responses/Unauthorized")
     * )
     * @Route("/customers/{username}", name="show_customer", methods={"GET"})
     */
    public function show(Customer $customer)
    {
        $user = $this->getUser();
        if ($user === $customer || $user->getRoles()[0] === 'ROLE_ADMIN') {
            return $this->requestManager->successResponseWithCache()->setContent($this->serializer->serialize($user, 'json'));
        } else {
            return $this->json(['message' => 'Access denied'], 403);
        }
    }

    /**
     * @OA\Get(
     *      path="/customers",
     *      security={"bearer"},
     *      tags={"Admin"},
     *      @OA\Parameter(ref="#/components/parameters/page"),
     *      @OA\Response(
     *          response="200",
     *          description="List of customers ressources",
     *          @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Customer"))
     *      ),
     *      @OA\Response(response="403",ref="#/components/responses/Unauthorized")
     * )
     * @Route("/customers/{page<\d+>?1}", name="list_customers", methods={"GET"})
     */
    public function index(Request $request, CustomerRepository $repo)
    {
        $role = $this->getUser()->getRoles();
        if ($role[0] !== 'ROLE_ADMIN') {
            return $this->json(['message' => 'Access denied'], 403);
        }
        $customers = $repo->findAllCustomers($this->requestManager->getPage($request), $this->getParameter('limit'))->getIterator();
        return $this->requestManager->successResponseWithCache()->setContent($this->serializer->serialize($customers, 'json'));
    }

    /**
     * @OA\Get(
     *      path="/customers/{username]/users",
     *      security={"bearer"},
     *      tags={"Admin"},
     *      @OA\Parameter(ref="#/components/parameters/username"),
     *      @OA\Parameter(ref="#/components/parameters/page"),
     *      @OA\Response(
     *          response="200",
     *          description="List of users ressource linked to a customer",
     *          @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/User"))
     *      ),
     *      @OA\Response(response="403",ref="#/components/responses/Unauthorized")
     * )
     * 
     * @Route("/customers/{username}/users{page<\d+>?1}", name="list_customer_users", methods={"GET"})
     */
    public function customerUserList(Request $request, Customer $customer, UserRepository $repo)
    {
        $role = $this->getUser()->getRoles();
        if ($role[0] !== 'ROLE_ADMIN') {
            return $this->json(['message' => 'Access denied'], 403);
        }
        $users = $repo->findAllCustomerUsers($customer, $this->requestManager->getPage($request), $this->getParameter('limit'))->getIterator();
        return $this->requestManager->successResponseWithCache()->setContent($this->serializer->serialize($users, 'json'));
    }
}
