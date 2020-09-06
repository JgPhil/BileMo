<?php

namespace App\Listeners;

use JMS\Serializer\SerializerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Response\JWTAuthenticationSuccessResponse;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class AuthenticationSuccessListener
{
    private $jwtTokenTTL;

    private $cookieSecure = false;

    private $serializer;

    private $normalizer;

    public function __construct($ttl, SerializerInterface $serializer, NormalizerInterface $normalizer)
    {
        $this->jwtTokenTTL = $ttl;
        $this->serializer = $serializer;
        $this->normalizer = $normalizer;
    }

    /**
     * This function is responsible for the authentication part
     *
     * @param AuthenticationSuccessEvent $event
     * @return JWTAuthenticationSuccessResponse
     */
    public function onAuthenticationSuccess(AuthenticationSuccessEvent $event)
    {
        /** @var JWTAuthenticationSuccessResponse $response */
        $response = $event->getResponse();
        $data = $event->getData();
        $tokenJWT = $data['token'];
       /*  $data['user'] = $this->normalizer->normalize($event->getUser(), null,[AbstractNormalizer::IGNORED_ATTRIBUTES => ['password', 'salt', 'users', 'roles']]); */
        $event->setData($data);
        $response->headers->setCookie(new Cookie('BEARER', $tokenJWT, (new \DateTime())
            ->add(new \DateInterval('PT' . $this->jwtTokenTTL . 'S')), '/', null, $this->cookieSecure));
        return $response;
    }
}
