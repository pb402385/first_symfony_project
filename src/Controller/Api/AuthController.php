<?php
// src/Controller/Api/AuthController.php
namespace App\Controller\Api;

use App\Entity\User;
use App\Repository\RevokedTokenRepository;
use App\Security\JwtTokenManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class AuthController extends AbstractController
{
    #[Route('/api/login', name: 'api_login', methods: ['POST'])]
    public function login(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager,
        JwtTokenManager $jwtManager
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        if (empty($data['email']) || empty($data['password'])) {
            return $this->json(['message' => 'Email et mot de passe requis'], 400);
        }

        $user = $entityManager->getRepository(User::class)
            ->findOneBy(['email' => $data['email']]);

        if (!$user || !$passwordHasher->isPasswordValid($user, $data['password'])) {
            return $this->json(['message' => 'Identifiants incorrects'], 401);
        }

        if (!$user->isVerified()) {
            return $this->json(['message' => 'Veuillez vérifier votre adresse email via le lien fournit dans votre boîte de messagerie'], 403);
        }

        $token = $jwtManager->createToken($user);

        return $this->json([
            'token' => $token,
            'user' => [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'roles' => $user->getRoles(),
            ]
        ]);
    }


    #[Route('/api/logout', name: 'api_logout', methods: ['POST'])]
    public function logout(
        Request $request,
        RevokedTokenRepository $revokedTokenRepository
    ): JsonResponse
    {
        $authHeader = $request->headers->get('Authorization');

        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            return new JsonResponse(['error' => 'No token provided'], 400);
        }

        $token = substr($authHeader, 7);

        // Récupérer l'expiration du JWT (ou mettre une durée par défaut)
        $expiresAt = new \DateTimeImmutable('+1 hour'); // à adapter selon ton JWT

        $revokedTokenRepository->revoke($token, $expiresAt);

        return new JsonResponse([
            'message' => 'Déconnexion réussie. Supprimez le token côté client.'
        ]);
    }

}
