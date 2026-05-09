<?php
// src/Service/TextFormatter.php
namespace App\Service\Mail;

use App\Entity\User;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;

use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;

class MailService
{

    public function __construct(private VerifyEmailHelperInterface $emailVerifier){

    }
    public function sendMail(User $user, MailerInterface $mailer): Response
    {

            $target = 'noreply@docshare.fr';
            $mail = (new TemplatedEmail())
                ->from($target)
                ->to($user->getEmail())
                ->subject('Titre mail sur DocShare')
                ->htmlTemplate('mail/mail-page.html.twig')
                ->context(['user' => $user,
                ]);
            try {
                $mailer->send($mail);
                return new Response('SUCCESS: SEND MAIL OK, user:' . $user->getEmail() );
            } catch (\Exception $exception) {
                return new Response('ERROR: SEND MAIL KO, user:' . $user->getEmail() );
            }

    }

    public function sendRegisterMail(User $user, MailerInterface $mailer): Response
    {

        $target = 'noreply@docshare.fr';
        $site = 'https://localhost:8000/';

        try {
            $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
                (new TemplatedEmail())
                    ->from(new Address($target, 'Mon Site'))
                    ->to((string) $user->getEmail())
                    ->subject('Confirmez votre email')
                    ->htmlTemplate('mail/register.html.twig')
            );
            return new Response('SUCCESS: envoit de l\'email d\'inscription OK, user:' . $user->getEmail() );
        } catch (\Exception $exception) {
            return new Response('ERROR: envoit de l\'email d\'inscription KO, user:' . $user->getEmail() );
        }

    }

    public function verifyUserEmail(Request $request): Response
    {

        try {
            $this->emailVerifier->handleEmailConfirmation($request, $this->getUser());
        } catch (VerifyEmailExceptionInterface $exception) {
            return new Response('KO');
        }
        return new Response('OK');

    }
}
