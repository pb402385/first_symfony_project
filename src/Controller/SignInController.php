<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

class SignInController extends AbstractController
{
    #[Route('/home', name: 'app_sign_sign')]
    public function index(): Response
    {
        // Render a Twig template
        return $this->render('index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }


    // Dans SecurityController.php
    #[Route('/register', name: 'auth.register')]
    public function register(): Response
    {
        $error = '';
        return $this->render('security/register.html.twig', [
            'title' => 'Register:',
            'error' => $error,
        ]);
    }
}
