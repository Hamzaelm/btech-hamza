<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;

class AuthController extends AbstractController
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }
    
    #[Route('/api/register',name:'register', methods: ['POST'])]
    public function register(Request $request, UserPasswordHasherInterface $encoder, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $user = new User();
        
        $user->setUsername($data['username']);
        $password = $encoder->hashPassword($user, $data['password']);
        $user->setPassword($password);
        $user->setRoles(['ROLE_USER']);

        $em->persist($user);
        $em->flush();

        return new JsonResponse(['message' => 'User created successfully'], 201);
    }

    #[Route('/api/login',name:'login', methods: ['POST'])]
    public function login(): JsonResponse
    {
        $user = $this->security->getUser();
        
        if ($user) {
            return new JsonResponse([
                'message' => 'Authentication successful',
                'token' => $this->getTokenForUser($user)
            ]);
        }

        return new JsonResponse(['message' => 'Authentication failed'], 401);
    }
    private function getTokenForUser($user)
    {
        $jwtManager = $this->get('lexik_jwt_authentication.jwt_manager');
        return $jwtManager->create($user);
    }
}