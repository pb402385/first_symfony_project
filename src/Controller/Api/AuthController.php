<?php
// src/Controller/Api/AuthController.php
namespace App\Controller\Api;

use App\Form\LoginType;
use App\Repository\RevokedTokenRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AuthController extends AbstractController
{

    public function __construct(
        private RevokedTokenRepository $revokedTokenRepository,
    ) {}

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
    ): void
    {
        // Symfony gère ça automatiquement

    }

    #[Route('/revokeToken', name: 'revoke_token', methods: ['GET', 'POST'])]
    public function revoke(
        Request $request,
    ): Response
    {
        // on revoke le token
        $authHeader = $request->headers->get('Authorization');

        //dd($authHeader, $request, $user, $event);

        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            dump("TOKEN Absent ou érroné");
        } else {
            $token = substr($authHeader, 7);

            //dd($token);

            // Récupérer l'expiration du JWT (ou mettre une durée par défaut)
            $expiresAt = new \DateTimeImmutable('+1 hour'); // à adapter selon ton JWT

            $this->revokedTokenRepository->revoke($token, $expiresAt);

            return $this->json([
                'message' => 'Token révoqué avec succès!',
            ], 200);
        }

        return $this->json([
            'message' => 'Impossible de révoquer le token!',
        ], 500);
    }


}
