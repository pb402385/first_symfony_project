<?php
// src/Controller/Api/AuthController.php
namespace App\Controller\Api;

use App\Entity\User;
use App\Form\LoginType;
use App\Repository\RevokedTokenRepository;
use App\Security\JwtTokenManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AuthController extends AbstractController
{

    #[Route('/login', name: 'app_login', methods: ['GET', 'POST'])]
    public function login(AuthenticationUtils $authenticationUtils, Request $request): Response
    {
        // Si déjà connecté → redirection
        if ($this->getUser()) {
            return $this->redirectToRoute('home.index');
        }

        $form = $this->createForm(LoginType::class);
        $form->handleRequest($request);   // ← Important

        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        //dd($error);

        // Important : on renvoie du HTML normal (Turbo acceptera car ce n'est pas une réponse de formulaire POST)
        return $this->render('security/login.html.twig', [
            'form' => $form->createView(),
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route('/logout', name: 'app_logout')]
    public function logout(
        Request $request,
        RevokedTokenRepository $revokedTokenRepository
    ): void
    {
        // Symfony gère ça automatiquement

        // on revoke le token
        $authHeader = $request->headers->get('Authorization');

        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            dump("TOKEN Absent ou érroné");
        } else {
            $token = substr($authHeader, 7);

            // Récupérer l'expiration du JWT (ou mettre une durée par défaut)
            $expiresAt = new \DateTimeImmutable('+1 hour'); // à adapter selon ton JWT

            $revokedTokenRepository->revoke($token, $expiresAt);
        }

    }

}
