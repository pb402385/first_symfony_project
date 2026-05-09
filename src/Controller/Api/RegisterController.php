<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Service\Mail\MailService;
use App\Security\JwtTokenManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RegisterController extends AbstractController
{

    public function __construct(private MailService $mailService,){

    }

    #[Route('/api/register', name: 'api_register', methods: ['POST'])]
    public function register(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $em,
        JwtTokenManager $jwtManager,
        MailerInterface $mailer
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        // Validation basique
        if (empty($data['email']) || empty($data['password'])) {
            return $this->json(['message' => 'Tous les champs obligatoires doivent être remplis'], 400);
        }

        if ($data['password'] !== ($data['password_confirm'] ?? null)) {
            return $this->json(['message' => 'Les mots de passe ne correspondent pas'], 400);
        }

        // Vérifier si l’email existe déjà
        $existingUser = $em->getRepository(User::class)->findOneBy(['email' => $data['email']]);
        if ($existingUser) {
            return $this->json(['message' => 'Cet email est déjà utilisé'], 409);
        }

        $user = new User();
        $user->setEmail($data['email']);
        $user->setRoles(['ROLE_USER']);
        $user->setCreatedAt(new \DateTimeImmutable('now'));
        $user->setIsVerified(false);
        $user->setName($data['email']);


        // Hash du mot de passe
        $hashedPassword = $passwordHasher->hashPassword($user, $data['password']);
        $user->setPassword($hashedPassword);

        $em->persist($user);
        $em->flush();

        //On envoit un email au client afin qu'il finalise son inscription
        $email = (new TemplatedEmail())
            ->from(new Address('noreply@docshare.fr', 'DocShare'))
            ->to($user->getEmail())
            ->subject('Confirmez votre adresse email')
            ->htmlTemplate('mail/register.html.twig')
            ->context(['data' => $data,
            ]);

        $this->mailService->sendEmailConfirmation('app_verify_email', $user, $email);

        // Génération du token JWT
        $token = $jwtManager->createToken($user);

        return $this->json([
        'message' => 'Compte créé avec succès',
        'token' => $token,
        'user' => [
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'roles' => $user->getRoles(),
            ]
        ], 201);
    }


    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request): Response
    {
        /*
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        try {
            $this->emailVerifier->handleEmailConfirmation($request, $this->getUser());
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $exception->getReason());
            return $this->redirectToRoute('app_register');
        }

        $this->addFlash('success', 'Votre adresse e-mail a été vérifiée.');
        */
        return $this->redirectToRoute('app_homepage');
    }
}
