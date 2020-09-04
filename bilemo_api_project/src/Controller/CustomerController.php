<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Repository\CustomerRepository;
use JMS\Serializer\Serializer as SerializerSerializer;
use JMS\Serializer\SerializerInterface as SerializerSerializerInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/api/v1")
 */
class CustomerController extends AbstractController
{
    /**
     * @Route("/customers/{username}", name="show_customer", methods={"GET"})
     */
    public function show(Customer $customer)
    {
        $user = $this->getUser();

        if ($user == $customer) {
            return $this->json(
                $customer,
                200,
                [
                    'Cache-Control' => 'public',
                    'maxage' => 3600,
                    'must-revalidate' => true
                ],
                [
                    'groups' => 'customer_read'
                ]
            );
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
    public function index(Request $request, CustomerRepository $repo, SerializerSerializerInterface $JMSSerializer)
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
        $customers = $repo->findAllCustomers($page, $this->getParameter('limit'))->getIterator();
        $data = $JMSSerializer->serialize($customers, 'json');
        $response = new Response($data, 200, []);
        $response->headers->set('Content-Type', 'application/json');
        $response->mustRevalidate();
        return $response;


        /* return $this->json(
            $repo->findAllCustomers($page, $this->getParameter('limit')),
            200,
            [
                'Cache-Control' => 'public',
                'maxage' => 3600,
                'must-revalidate' => true
            ],
            [
                'groups' => 'customer_read'
            ] 
        );*/
    }
}
