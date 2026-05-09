<?php
namespace App\Service\Mail;

use App\Entity\User;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;

use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;
use Symfony\Component\HttpFoundation\Request;


class MailService
{

    public function __construct(
        private VerifyEmailHelperInterface $verifyEmailHelper,
        private MailerInterface $mailer,
    ) {}

    public function sendMail(TemplatedEmail $email): Response
    {

            try {
                $this->mailer->send($email);
                return new Response('SUCCESS: SEND MAIL OK, user:' . $user->getEmail() );
            } catch (\Exception $exception) {
                return new Response('ERROR: SEND MAIL KO, user:' . $user->getEmail() );
            }

    }

    public function sendEmailConfirmation(string $verifyEmailRouteName, User $user, TemplatedEmail $email): void
    {
        $signatureComponents = $this->verifyEmailHelper->generateSignature(
            $verifyEmailRouteName,
            (string) $user->getId(),
            (string) $user->getEmail(),
            ['id' => $user->getId()] // optionnel
        );

        $context = $email->getContext();
        $context['signedUrl'] = $signatureComponents->getSignedUrl();
        $context['expiresAtMessageKey'] = $signatureComponents->getExpirationMessageKey();
        $context['expiresAtMessageData'] = $signatureComponents->getExpirationMessageData();

        $email->context($context);

        $this->mailer->send($email);
    }

    /**
     * Valide le lien de confirmation
     */
    public function validateEmailConfirmation(Request $request, User $user): void
    {
        $this->verifyEmailHelper->validateEmailConfirmation(
            $request->getUri(),
            (string) $user->getId(),
            (string) $user->getEmail()
        );
    }
}
