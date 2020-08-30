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

/**
 * @Route("/api")
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
     * @Route("/customers/{page<\d+>?1}", name="list_customers", methods={"GET"})
     */
    public function index(Request $request, CustomerRepository $repo)
    {
        $page = $request->query->get('page');
        if (is_null($page) || $page < 1) {
            $page = 1;
        }
        return $this->json($repo->findAllCustomers($page, $this->getParameter('limit')), 200, [], ['groups' => 'customer_read']);
    }
}
