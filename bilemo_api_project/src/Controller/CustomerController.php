<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Repository\CustomerRepository;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use OpenApi\Annotations as OA;

/**
 * @Route("/api/v1")
 */
class CustomerController extends AbstractController
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

        if ($user == $customer) {
            return $this->json($customer, 200, [], ['groups' => 'customer_read']);
        } else {
            $data = [
                'message' => 'Access denied'
            ];
            return $this->json($data, 403);
        }
    }

    /**
     * @OA\Get(
     *      path="/customers",
     *      security={"bearer"},
     *      tags={"Customers"},
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
            $data = [
                'message' => 'Access denied'
            ];
            return $this->json($data, 403);
        }
        $page = $request->query->get('page');
        if (is_null($page) || $page < 1) {
            $page = 1;
        }
        return $this->json($repo->findAllCustomers($page, $this->getParameter('limit')), 200, [], ['groups' => 'customer_read']);
    }
}
