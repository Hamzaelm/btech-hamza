<?php

namespace App\Controller;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class DashboardController extends AbstractController
{
    #[Route('/api/user', methods: ['GET'])]
    public function getUserInfo(Request $request): JsonResponse
    {
        $user = $this->getUser();

        if (!$user) {
            return new JsonResponse(['message' => 'Unauthorized'], 401);
        }

        return new JsonResponse([
            'id' => $user->getId(),
            'username' => $user->getUsername(),
            'role' => $user->getRoles()
        ]);
    }
    #[Route('/api/user/update-password', methods: ['PATCH'])]
    public function updatePassword(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $em
    ): JsonResponse {
        $user = $this->getUser();

        if (!$user) {
            return new JsonResponse(['message' => 'Unauthorized'], 401);
        }

        $data = json_decode($request->getContent(), true);

        if (empty($data['currentPassword']) || empty($data['newPassword'])) {
            return new JsonResponse(['message' => 'Current and new password are required'], 400);
        }

        if (!$passwordHasher->isPasswordValid($user, $data['currentPassword'])) {
            return new JsonResponse(['message' => 'Invalid current password'], 400);
        }

        $newHashedPassword = $passwordHasher->hashPassword($user, $data['newPassword']);
        $user->setPassword($newHashedPassword);

        $em->persist($user);
        $em->flush();

        return new JsonResponse(['message' => 'Password updated successfully!']);
    }

}