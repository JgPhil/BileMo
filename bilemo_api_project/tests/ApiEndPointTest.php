<?php

namespace App\Tests;

use PHPUnit\Framework\TestCase;
use App\Repository\CustomerRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ApiEndPointTest extends WebTestCase
{

    public function testIndexEndPointAnonymous()
    {
        $client = static::createClient();
        $method = 'GET';
        $uri = '/api';
        $this->assertEquals(401, $response = $this->testEndPoint($client, $method, $uri));
    }

    public function testIndexEndPointGranted()
    {
        $client = $this->AuthenticateCustomer(static::createClient());
        $method = 'GET';
        $uri = '/api';
        $this->assertEquals(200, $this->testEndPoint($client, $method, $uri));
    }

    private function AuthenticateCustomer(KernelBrowser $client)
    {
        $customerRepository = static::$container->get(CustomerRepository::class);
        $customer = $customerRepository->findOneBy(['username' => 'phone-discount.fr']);
        return $client->loginUser($customer);
    }

    private function testEndPoint(KernelBrowser $client, string $method, string $uri)
    {
        $client->request($method, $uri);
        return $client->getResponse()->getStatusCode();
    }
}
