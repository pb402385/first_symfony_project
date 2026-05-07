<?php

namespace App\Controller;

use App\DTO\ContactDTO;
use App\Form\ContactType;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

final class ContactController extends AbstractController
{
    public function __construct(){

    }
    #[Route('/contact', name: 'contact')]
    public function contact(Request $request, MailerInterface $mailer): Response
    {
        $data = new ContactDTO();

        $form = $this->createForm(ContactType::class, $data);
        $form->handleRequest($request);
        if( $form->isSubmitted() && $form->isValid() ) {
            $data = $form->getData();
            $mail = (new TemplatedEmail())
                ->from($data->target)
                ->to($data->email)
                ->subject('Contact Email!')
                ->htmlTemplate('mail/contact.html.twig')
                ->context(['data' => $data,
                    ]);
            try {
                $mailer->send($mail);
                $this->addFlash('success','Le Mail a bien été envoyé');
                return $this->redirectToRoute('home.index');
            } catch (\Exception $exception) {
                $this->addFlash('danger', $exception->getMessage());
            }
        }
        return $this->render('contact/contact.html.twig', [
            'controller_name' => 'ContactController',
            'form' => $form,
        ]);
    }
}
