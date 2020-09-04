<?php

namespace App\Controller;

use JMS\Serializer\SerializationContext;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DefaultController extends AbstractController
{


    protected $successResponse;

    protected $userListContext;
    
    protected $userDetailContext;

    protected $customerListContext;
    
    protected $customerDetailContext;

    public function __construct() 
    {        

        $this->userListContext = SerializationContext::create()->setGroups(array('list_user', 'list_customer'));
        $this->userDetailContext = SerializationContext::create()->setGroups(array('detail_user'));
        $this->customerListContext = SerializationContext::create()->setGroups(array('list_customer'));
        $this->customerDetailContext = SerializationContext::create()->setGroups(array('detail_customer'));

        $this->successResponse = new Response();
        $this->successResponse->setStatusCode(200);
        $this->successResponse->headers->set('Content-Type', 'application/json');
        $this->successResponse->mustRevalidate();
        $this->successResponse->setMaxAge(3600);
    }
}
