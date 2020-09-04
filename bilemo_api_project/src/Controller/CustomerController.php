<?php

namespace App\Controller;

use App\Entity\Customer;
use OpenApi\Annotations as OA;
use App\Repository\CustomerRepository;
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
class CustomerController extends AbstractController
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
    public function show(Customer $customer, SerializerInterface $serializer)
    {
        $user = $this->getUser();

        if ($user == $customer || $user->getRoles()[0] === 'ROLE_ADMIN') {
            return $this->successResponse->setContent($serializer->serialize($user, 'json'));
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
     * @IsGranted("ROLE_ADMIN")
     */
    public function index(Request $request, CustomerRepository $repo, SerializerInterface $serializer)
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
        return $this->successResponse->setContent($serializer->serialize($customers, 'json', $this->listSerialisationContext));
    }
}
